<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Twig\Components;

    use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashDomainRepository;
    use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
    use Symfony\UX\LiveComponent\Attribute\LiveProp;
    use Symfony\UX\LiveComponent\ComponentToolsTrait;
    use Symfony\UX\LiveComponent\DefaultActionTrait;
    use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

    #[AsLiveComponent('NeoxSearchDomain', template: '@NeoxDashBoardBundle/Components/NeoxSearchDomain.html.twig')]
    final class NeoxSearchDomain
    {

//        use ComponentToolsTrait;
        use DefaultActionTrait;

        #[LiveProp(writable: true, url: true)]
        public ?string $query = null;

        public function __construct(private NeoxDashDomainRepository $domainRepository)
        {
        }

        public function getPackages(): array
        {
            return $this->domainRepository->findByUrl($this->query);
        }
    }
