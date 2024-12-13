<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Twig\Components;

    use Doctrine\ORM\EntityManagerInterface;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashClass;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashDomain;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashFavorite;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashSection;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashWidget;
    use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashClassRepository;
    use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashFavoriteRepository;
    use NeoxDashBoard\NeoxDashBoardBundle\Services\ToolsBoxService;
    use Random\RandomException;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\EventDispatcher\EventDispatcherInterface;
    use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
    use Symfony\UX\LiveComponent\Attribute\LiveAction;
    use Symfony\UX\LiveComponent\Attribute\LiveArg;
    use Symfony\UX\LiveComponent\Attribute\LiveProp;
    use Symfony\UX\LiveComponent\ComponentToolsTrait;
    use Symfony\UX\LiveComponent\DefaultActionTrait;
    use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\Common\Collections\Collection;


    #[AsLiveComponent('NeoxFavoriteDomain', template: '@NeoxDashBoardBundle/Components/NeoxFavoriteDomain.html.twig')]
    //    #[AsLiveComponent('NeoxFavoriteDomain', template: '@NeoxDashBoardBundle/neox_favorite/widget/NeoxFavoriteDomain.html.twig')]
    final class NeoxFavoriteDomain extends AbstractController
    {

        //        use ComponentToolsTrait;
        use DefaultActionTrait;
        use ComponentToolsTrait;

        //        #[LiveProp(writable: true)]
        public ?array $NeoxDashClass = null;


        public function __construct(private readonly NeoxDashClassRepository $classRepository, private readonly NeoxDashFavoriteRepository $favoriteRepository, private readonly EntityManagerInterface $entityManager, private readonly EventDispatcherInterface $eventDispatcher, private readonly ToolsBoxService $toolsBoxService) {}

        #[LiveAction]
        public function mode(#[LiveArg] string $query = "link"): void
        {
            $entity = $this->entityManager
                ->getRepository(NeoxDashSection::class)
                ->findOneBy([ "id" => $query ])
            ;

            if ($entity) {
                // Reverse the value of the 'edit' field
                $entity->setEdit(!$entity->getEdit());

                // Persist the entity and save in base
                $this->entityManager->persist($entity);
                $this->entityManager->flush();

                $this->refresh($entity
                    ->getClass()
                    ->getId());
            }
        }

        #[LiveAction]
        public function toggleFavorite(#[LiveArg] string $id, #[LiveArg] string $section): void
        {
            try {
                // Récupérer l'entité Domain à partir de son ID
                $domain = $this->entityManager
                    ->getRepository(NeoxDashDomain::class)
                    ->find($id)
                ;

                if (!$domain) {
                    throw new \Exception('Domain not found.');
                }

                // Récupérer ou initialiser l'entité Favorite associée
                $favorite = $domain->getFavorite();

                if (!$favorite) {
                    $favorite = new NeoxDashFavorite();
                    $favorite->setFavorite(false); // Valeur par défaut
                }

                // Inverser l'état du champ favorite
                $favorite->setFavorite(!$favorite->getFavorite());

                // Persister l'entité Favorite
                $this->entityManager->persist($favorite);

                // Optionnel : Associer l'entité Favorite au Domain si ce n'est pas encore fait
                if (!$domain->getFavorite()) {
                    $domain->setFavorite($favorite);
                    $this->entityManager->persist($domain);
                }

                // Sauvegarder les changements
                $this->entityManager->flush();

                // Notifier le front-end via Mercure et TurboStream
                $this->updateFront($domain, $section);

            } catch (\Exception $e) {
                // Gérer les erreurs
                error_log('Error toggling favorite: ' . $e->getMessage());
                // Vous pourriez également lever une exception ou retourner une réponse adaptée si nécessaire
            }

        }

        /**
         * @throws RandomException
         */
        #[LiveAction]
        public function getFavorite(): array
        {
            /**  ====== Find widget FAVORITE ========
             */
            $widgetFavorite = $this->entityManager
                ->getRepository(NeoxDashWidget::class)
                ->findOneByPublish("Favorite")
            ;
            /** @var NeoxDashFavorite[] $favorites
             */
            $favorites = $this->favoriteRepository->findOnlyFavorites();

            foreach ($favorites as $favorite) {
                foreach ($favorite->getNeoxDashDomains() as $domain) {
                    $widgetFavorite
                        ->getSection()
                        ->addNeoxDashDomain($domain)
                    ;
                }
            }

            return [
                $widgetFavorite
                    ->getSection()
                    ->getClass()
            ];
        }

        /**
         * @throws RandomException
         */
        #[LiveAction]
        public function refresh(#[LiveArg] string $query = "link"): void
        {
            $NeoxDashClass = $this->entityManager
                ->getRepository(NeoxDashClass::class)
                ->findOneClass($query)
            ;
            // $this->NeoxDashClass    = $this->getFavorite();
            $this->NeoxDashClass = [ $NeoxDashClass ];

        }

        /**
         * @throws RandomException
         */
        #[LiveAction]
        public function refreshFavorite(#[LiveArg] string $query = "link"): void
        {
            $this->NeoxDashClass = $this->getFavorite();
        }

        /**
         * @throws RandomException
         */
        private function updateFront($domain, $section): void
        {
            $classFavorite = $this->toolsBoxService->getNeoxFavorite();

            if ($section === "Favorite") {

                $this->refreshFavorite();

                // Refresh only the Domain
                $this->dispatchBrowserEvent('favorite:refresh', [
                    "action"      => "refresh",
                    "idComponent" => "live-NeoxDashBoardContent@" . $domain
                            ->getSection()
                            ->getClass()
                            ->getId(),
                    "idClass"     => $domain
                    ->getSection()
                    ->getClass()
                    ->getId(),
                ]);

            }
            else {

                // refresh content class was select only
                $this->refresh($domain
                    ->getSection()
                    ->getClass()
                    ->getId());

                /*
                * this code will refresh make event on front to refresh only Widget Favorite
                */

                //            $this->refresh($classFavorite->getId());
                $this->dispatchBrowserEvent('favorite:refresh', [
                    "action"      => "refreshFavorite",
                    "idComponent" => "live-NeoxDashBoardContent@" . $classFavorite->getId(),
                    "idClass"     => $classFavorite->getId()
                ]);


            }
        }
    }
