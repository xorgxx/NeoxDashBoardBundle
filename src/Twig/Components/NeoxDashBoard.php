<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Twig\Components;


    use Doctrine\ORM\EntityManagerInterface;
//    use phpDocumentor\Reflection\Types\Collection;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashSetup;
    use NeoxDashBoard\NeoxDashBoardBundle\Pattern\SetupHelper;
    use Psr\Log\LoggerInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\RequestStack;
    use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
    use Symfony\UX\LiveComponent\Attribute\LiveAction;
    use Symfony\UX\LiveComponent\Attribute\LiveArg;
    use Symfony\UX\LiveComponent\Attribute\LiveListener;
    use Symfony\UX\LiveComponent\Attribute\LiveProp;
    use Symfony\UX\LiveComponent\ComponentToolsTrait;
    use Symfony\UX\LiveComponent\DefaultActionTrait;

    #[AsLiveComponent('NeoxDashBoard', template: '@NeoxDashBoardBundle/Components/NeoxDashBoard.html.twig')]
    final class NeoxDashBoard extends abstractController
    {
        use DefaultActionTrait;

//        #[LiveProp]
        public ?NeoxDashSetup $NeoxDashSetup = null;


        public function __construct(private readonly EntityManagerInterface $entityManager, private readonly SetupHelper $setupHelper, readonly LoggerInterface $logger)
        {
        }

        public function mount(?NeoxDashSetup $NeoxDashSetup): void
        {
            $this->NeoxDashSetup = $this->initIfNeed($NeoxDashSetup);
        }

        private function initIfNeed(?NeoxDashSetup $NeoxDashSetup){
            if (!$NeoxDashSetup) {
                $p = new NeoxDashSetup();
                $p->setCountry("Fr");
                $p->setTheme("#d5cdcd");
                $repository = $this->entityManager;
                $repository->persist($p);
                $repository->flush($p);
                return $p;
            }
            return $NeoxDashSetup;
        }

    }
