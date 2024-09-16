<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Twig\Components;

    use Symfony\UX\LiveComponent\Attribute\LiveAction;
    use Symfony\UX\LiveComponent\Attribute\LiveProp;
    use Symfony\UX\LiveComponent\DefaultActionTrait;
    use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

    #[AsTwigComponent('BootstrapModal', template: '@NeoxDashBoardBundle/Components/BootstrapModal.html.twig')]
    class BootstrapModal
    {
//        use DefaultActionTrait;

        #[LiveProp]
        public string $id;

        #[LiveProp]
        public ?string $title = "test";

        #[LiveProp]
        public ?string $body = "tyty";

    }
