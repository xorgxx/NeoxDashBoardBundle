<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Twig\Components;

    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashClass;
    use Doctrine\ORM\EntityManagerInterface;
//    use phpDocumentor\Reflection\Types\Collection;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashDomain;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashSection;
    use phpDocumentor\Reflection\Types\Integer;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\RequestStack;
    use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
    use Symfony\UX\LiveComponent\Attribute\LiveAction;
    use Symfony\UX\LiveComponent\Attribute\LiveArg;
    use Symfony\UX\LiveComponent\Attribute\LiveProp;
    use Symfony\UX\LiveComponent\DefaultActionTrait;
    use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

    #[AsLiveComponent('NeoxDashBoardContent', template: '@NeoxDashBoardBundle/Components/NeoxDashBoardContent.html.twig')]
    final class NeoxDashBoardContent extends abstractController
    {
        use DefaultActionTrait;

//        #[LiveProp]
        public  ?NeoxDashClass $NeoxDashClass = null;
        public  array   $loopn          ;

        #[LiveProp(writable: true)]
        public ?string         $query         = null;

//        #[LiveProp(writable: true)]
//        public ?string         $id         = null;

        public function __construct(private readonly EntityManagerInterface $entityManager, private readonly RequestStack $requestStack ){}

//        public function mount(?NeoxDashClass $NeoxDashClass): void
//        {
//            $this->NeoxDashClass    = $NeoxDashClass;
//        }
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
        public function toggleFavorite(#[LiveArg] string $id): void
        {
            // Récupérer l'entité Favorite à partir de son ID
            $favorite = $this->entityManager->getRepository(NeoxDashDomain::class)->findOneBy(['id' => $id]);

            if ($favorite) {
                // Inverser la valeur du champ isFavorite
                $favorite->setFavorite(!$favorite->getIsFavorite());

                // Sauvegarder les modifications dans la base de données
                $this->entityManager->persist($favorite);
                $this->entityManager->flush();

                // Rafraîchir l'état de l'élément, si nécessaire
                $this->refresh($favorite->getSection()->getClass()->getid());
            }
        }

        #[LiveAction]
        public function refresh(#[LiveArg] string $query="link"): void
        {
            $this->NeoxDashClass = $this->entityManager->getRepository(NeoxDashClass::class)->findOneBy(["id" => $query]) ;
        }

    }
