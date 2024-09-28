<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Twig\Components;

    use Symfony\UX\LiveComponent\Attribute\LiveAction;
    use Symfony\UX\LiveComponent\Attribute\LiveProp;
    use Symfony\UX\LiveComponent\DefaultActionTrait;
    use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
    use Symfony\UX\LiveComponent\ComponentToolsTrait;

    #[AsTwigComponent('BootstrapModal', template: '@NeoxDashBoardBundle/Components/BootstrapModal.html.twig')]
    class BootstrapModal
    {
//        use DefaultActionTrait;
        use ComponentToolsTrait;

        #[LiveProp]
        public array $crudIni;

        #[LiveProp]
        public ?string $title = "test";

        #[LiveProp]
        public ?string $body = "tyty";

        public function mount(): void
        {

        }
        #[PostMount]
        public function postMount(): void
        {
            $this->emitUp('productAdded');
        }
    }
