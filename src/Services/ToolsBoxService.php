<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Services;

    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashClass;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashFavorite;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashSection;
    use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashDomainRepository;
    use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashFavoriteRepository;
    use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashWidgetRepository;
    use Random\RandomException;

    readonly class ToolsBoxService
    {


        public function __construct(public readonly NeoxDashWidgetRepository $dashWidgetRepository,
                                    private readonly NeoxDashDomainRepository $dashDomainRepository
        )
        {
        }

        /**
         * @throws RandomException
         */
        public static function getColor(): string
        {
            $r = random_int(0, 127); // Rouge
            $g = random_int(0, 127); // Vert
            $b = random_int(0, 127); // Bleu

            return sprintf("#%02x%02x%02x", $r, $g, $b);
        }

        /**
         */
        public function getNeoxFavorite(): ?NeoxDashClass
        {
            return  $this->dashWidgetRepository->findByWidgetGetClass() ?? null;
        }

        /**
         */
        public function getNeoxSearch(): ?NeoxDashClass
        {
            return  $this->dashWidgetRepository->findByWidgetGetClass("Search") ?? null;
        }

        /**
         */
        public function getNeoxDomain($id): ?NeoxDashClass
        {
            $o = $this->dashDomainRepository->findOneBy(['id' => $id]) ?? null;
            return  $o->getSection()->getClass();
        }
    }