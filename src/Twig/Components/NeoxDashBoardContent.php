<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Twig\Components;

    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashClass;
    use Doctrine\ORM\EntityManagerInterface;
//    use phpDocumentor\Reflection\Types\Collection;
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
                // Inverser la valeur du champ 'edit'
                $entity->setEdit(!$entity->getEdit());

                // Persister l'entité et sauvegarder en base
                $this->entityManager->persist($entity);
                $this->entityManager->flush();

                $this->refresh($entity->getClass()->getId());
//                $this->mount($this->NeoxDashClass);
            }

        }

        #[LiveAction]
        public function refresh(#[LiveArg] string $query="link"): void
        {
            $this->NeoxDashClass = $this->entityManager->getRepository(NeoxDashClass::class)->findOneBy(["id" => $query]) ;
        }

    }
