<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Twig\Components;

    use Doctrine\ORM\EntityManagerInterface;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashClass;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashDomain;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashFavorite;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashSection;
    use NeoxDashBoard\NeoxDashBoardBundle\Enum\NeoxDashTypeEnum;
    use NeoxDashBoard\NeoxDashBoardBundle\Enum\NeoxSizeEnum;
    use NeoxDashBoard\NeoxDashBoardBundle\Enum\NeoxStyleEnum;
    use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashDomainRepository;
    use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashFavoriteRepository;
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
    final class NeoxFavoriteDomain extends AbstractController
    {

//        use ComponentToolsTrait;
        use DefaultActionTrait;
        use ComponentToolsTrait;
//        #[LiveProp(writable: true)]
        public  ?array $NeoxDashClass = null;



        public function __construct(
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

            $favorite->setFavorite(!$favorite->getFavorite());
            $domain->setFavorite($favorite);

            $this->entityManager->persist($domain);
            $this->entityManager->flush();

            /*
             * ============== switch to make ux-live more advance as SAP ===========
             * PHP will emit signal to front page, Mercure and TurboStream will all the reste
             * to update specific section for as.
             */

            if ( $section === "FAVORITE" ) {
                // TODO : refresh only the favorite
                $this->dispatchBrowserEvent('favorite:refresh', [
                    "action"        => "refresh",
                    "idComponent"   => "live-NeoxDashBoardContent@" . $domain->getSection()->getClass()->getId(),
                    "idClass"       => $domain->getSection()->getClass()->getId()
                ]);
            }else{
                // refresh content class was select only
                $this->refresh($domain->getSection()->getClass()->getId());

                $this->dispatchBrowserEvent('favorite:refresh', [
                "action"        => "refreshFavorite",
                "idComponent"   => "live-NeoxFavorite@0",
                "idClass"       => $domain->getSection()->getClass()->getId()
            ]);


            }

        }


        #[LiveAction]
        public function getFavorite(): array
        {

            /** @var NeoxDashFavorite[] $favorites */
            $favorites          = $this->favoriteRepository->findOnlyFavorites();
            $sectionFavorite    = $this->entityManager->getRepository(NeoxDashSection::class)->findOneBy(['name' => "Widget@favorite"]);

            $section = (new NeoxDashSection())
                ->setName("FAVORITE")
                ->setHeaderColor($sectionFavorite->getHeaderColor())
                ->setRow($sectionFavorite->getRow())
                ->setSize($sectionFavorite->getSize())
            ;

            foreach ($favorites as $favorite) {
                foreach ($favorite->getNeoxDashDomains() as $domain) {
                    $section->addNeoxDashDomain($domain);
                }
            }
            $class = (new NeoxDashClass())
                ->addNeoxDashSection($section)
                ->setName("FAVORITE")
                ->setIcon("star")
                ->setMode(NeoxStyleEnum::TABS)
                ->setHeaderColor($sectionFavorite->getClass()->getHeaderColor())
                ->setSize($sectionFavorite->getClass()->getSize())
            ; // Retourne toutes les classes sous forme de collection

            return  [$class];
        }

        #[LiveAction]
        public function refresh(#[LiveArg] string $query="link"): void
        {
            $this->NeoxDashClass = [$this->entityManager->getRepository(NeoxDashClass::class)->findOneClass( $query)]; ;
        }

        #[LiveAction]
        public function refreshFavorite(#[LiveArg] string $query="link"): void
        {
            $this->NeoxDashClass = $this->getFavorite(); ;
        }
    }
