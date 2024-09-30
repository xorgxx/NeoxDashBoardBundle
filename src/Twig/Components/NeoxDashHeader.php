<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Twig\Components;

    use Doctrine\Common\Collections\Collection;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashClass;
    use Doctrine\ORM\EntityManagerInterface;

//    use phpDocumentor\Reflection\Types\Collection;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashSetup;
    use NeoxDashBoard\NeoxDashBoardBundle\Pattern\SetupHelper;
    use phpDocumentor\Reflection\Types\Integer;
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

    #[AsLiveComponent('NeoxDashHeader', template: '@NeoxDashBoardBundle/Components/NeoxDashHeader.html.twig')]
    final class NeoxDashHeader extends abstractController
    {
        use DefaultActionTrait;
        use ComponentToolsTrait;

        #[LiveProp]
        public ?NeoxDashSetup $NeoxDashSetup = null;

        #[LiveProp(writable: true)]
        public ?string $entityId = null;

        #[LiveProp(writable: true)]
        public ?string $query = null;


        public function __construct(private readonly EntityManagerInterface $entityManager, private readonly SetupHelper $setupHelper, readonly LoggerInterface $logger)
        {
        }

        public function mount(?NeoxDashSetup $NeoxDashSetup): void
        {
            $this->NeoxDashSetup = $NeoxDashSetup;
            $this->initializeSetup();
        }

        private function initializeSetup(): void
        {
            $this->setupHelper->initSeput($this->NeoxDashSetup);
        }


        #[LiveAction]
        public function refresh(#[LiveArg] string $query = "1"): void
        {
            $this->NeoxDashSetup = $this->entityManager->getRepository(NeoxDashSetup::class)->findOneBy([ "id" => $query ]);
            $this->initializeSetup($this->NeoxDashSetup);
//            $this->emit('refresh', [
//                'NeoxDashSetup' => $this->NeoxDashSetup->getId(),
//            ], componentName: 'NeoxDashBoard');
        }

    }
