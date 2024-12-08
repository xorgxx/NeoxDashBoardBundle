<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Form\type;

    use NeoxDashBoard\NeoxDashBoardBundle\Services\WidgetConfigService;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    class widgetType extends AbstractType
    {
        private WidgetConfigService $widgetConfigService;

        // Injecter le service qui contient la configuration des widgets
        public function __construct(WidgetConfigService $widgetConfigService)
        {
            $this->widgetConfigService = $widgetConfigService;
        }

        private function getParam(): array
        {
            // Récupérer la configuration des widgets depuis le service
            $widgets = $this->widgetConfigService->getWidgets();

            // Extraire les clés des widgets pour les utiliser comme options
            return array_keys($widgets);
        }

        // Configuration des options par défaut
        public function configureOptions(OptionsResolver $resolver): void
        {
            $widget = $this->getParam();
            $resolver->setDefaults([
                'choices'       => array_combine($widget, $widget),
                'label'         => 'Choose a widget',
                'placeholder'   => 'Select a widget',
            ]);
        }

        public function getParent(): string
        {
            return ChoiceType::class;  // Héritage de ChoiceType
        }
    }