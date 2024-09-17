<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Twig\Components;

    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashClass;
    use Doctrine\ORM\EntityManagerInterface;
//    use phpDocumentor\Reflection\Types\Collection;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\RequestStack;
    use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
    use Symfony\UX\LiveComponent\Attribute\LiveAction;
    use Symfony\UX\LiveComponent\Attribute\LiveArg;
    use Symfony\UX\LiveComponent\Attribute\LiveProp;
    use Symfony\UX\LiveComponent\DefaultActionTrait;

    #[AsLiveComponent('NeoxDashBoard', template: '@NeoxDashBoardBundle/Components/NeoxDashBoard.html.twig')]
    final class NeoxDashBoard extends abstractController
    {
        use DefaultActionTrait;

//        #[LiveProp]
        public  ?NeoxDashClass $NeoxDashClass  = null;

        #[LiveProp(writable: true)]
        public ?string $query    = null;


        public function __construct(private readonly EntityManagerInterface $entityManager, private readonly RequestStack $requestStack ){}

        public function mount(?NeoxDashClass $NeoxDashClass): void
        {
            $this->NeoxDashClass    = $NeoxDashClass;
            $this->currentContent   = $this->requestStack->getSession()->get('current_content', "default");
        }

        #[LiveAction]
        public function refresh(#[LiveArg] string $query="link"): void
        {
            $this->NeoxDashClass = $this->entityManager->getRepository(NeoxDashClass::class)->findOneBy(["id" => $query]) ;
        }

    }
