<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Services;


    use Psr\Container\NotFoundExceptionInterface;
    use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

    class WidgetConfigService
    {
        private array $widgetConfig;

        /**
         * @throws ContainerExceptionInterface
         * @throws NotFoundExceptionInterface
         */
        public function __construct(ParameterBagInterface  $parameterBag)
        {
            // Charge la configuration des widgets depuis le fichier neox_dash_board.yaml
            $this->widgetConfig = $parameterBag->get('neox_dash_board.widget') ?? [];
        }

        // Retourne la liste des widgets configurés
        public function getWidgets(): array
        {
            return $this->widgetConfig;
        }
    }