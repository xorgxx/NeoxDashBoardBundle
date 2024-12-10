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
        public  ?array $NeoxDashClass = null;


        public function __construct(
            private readonly NeoxDashClassRepository $classRepository,
            private readonly NeoxDashFavoriteRepository $favoriteRepository,
            private readonly EntityManagerInterface $entityManager,
            private readonly EventDispatcherInterface $eventDispatcher
            )
        {
        }

        #[LiveAction]
        public function mode(#[LiveArg] string $query="link"): void
        {
            $entity = $this->entityManager->getRepository(NeoxDashSection::class)->findOneBy(["id" => $query]) ;

            if ($entity) {
                // Reverse the value of the 'edit' field
                $entity->setEdit(!$entity->getEdit());

                // Persist the entity and save in base
                $this->entityManager->persist($entity);
                $this->entityManager->flush();

                $this->refresh($entity->getClass()->getId());
            }
        }

        #[LiveAction]
        public function toggleFavorite(#[LiveArg] string $id, #[LiveArg] string $section): void
        {
            // Récupérer l'entité Favorite à partir de son ID
            $domain = $this->entityManager->getRepository(NeoxDashDomain::class)->findOneBy(['id' => $id]);
            $favorite = $domain->getFavorite();
            if (!$favorite) {
                $favorite = new NeoxDashFavorite();
//                $favorite->setFavorite(true);
            }

            try {
                $favorite->setFavorite(!$favorite->getFavorite());
                $domain->setFavorite($favorite);

                $this->entityManager->persist($domain);
                $this->entityManager->flush();

                /*
                 * ============== switch to make ux-live more advance as SAP ===========
                 * PHP will emit signal to front page, Mercure and TurboStream will all the reste
                 * to update specific section for as.
                 */
                $favorite = $this->entityManager->getRepository(NeoxDashWidget::class)->findOneByPublish( "Favorite" );
                $this->updateFront($domain, $favorite, $section);
            } catch (\Exception $e) {
                return;
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
            $widgetFavorite     = $this->entityManager->getRepository(NeoxDashWidget::class)->findOneByPublish("Favorite");
            /** @var NeoxDashFavorite[] $favorites
             */
            $favorites          = $this->favoriteRepository->findOnlyFavorites();

            foreach ($favorites as $favorite) {
                foreach ($favorite->getNeoxDashDomains() as $domain) {
                    $widgetFavorite->getSection()->addNeoxDashDomain($domain);
                }
            }

            return  [$widgetFavorite->getSection()->getClass()];
        }

        /**
         * @throws RandomException
         */
        #[LiveAction]
        public function refresh(#[LiveArg] string $query="link"): void
        {
            if (!$NeoxDashClass = $this->entityManager->getRepository(NeoxDashClass::class)->findOneClass( $query )) {
                $this->NeoxDashClass    = $this->getFavorite();
            }else{
                $this->NeoxDashClass    = [$NeoxDashClass];
            }

        }

        /**
         * @throws RandomException
         */
        #[LiveAction]
        public function refreshFavorite(#[LiveArg] string $query="link"): void
        {
            $this->NeoxDashClass = $this->getFavorite(); ;
        }

        /**
         * @throws RandomException
         */
        private function updateFront($domain, $favorite, $section): void
        {
            if ( $section === "favorite" ) {
                // TODO : refresh only the favorite
                $this->dispatchBrowserEvent('favorite:refresh', [
                    "action"        => "refresh",
                    "idComponent"   => "live-NeoxDashBoardContent@" . $domain->getSection()->getClass()->getId(),
                    "idClass"       => $domain->getSection()->getClass()->getId()
                ]);
            }else {
                // refresh content class was select only
                $this->refresh($domain
                    ->getSection()
                    ->getClass()
                    ->getId());

                $this->dispatchBrowserEvent('favorite:refresh', [
                    "action"      => "refreshFavorite",
                    "idComponent" => "live-NeoxDashBoardContent@" . $favorite
                            ->getSection()
                            ->getClass()
                            ->getId(),
                    "idClass"     => $favorite
                        ->getSection()
                        ->getClass()
                        ->getId()
                ]);
            }
        }
    }
