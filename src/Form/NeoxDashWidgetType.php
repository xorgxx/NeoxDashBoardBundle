<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Form;

    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashWidget;
    use NeoxDashBoard\NeoxDashBoardBundle\Enum\NeoxDashTypeEnum;
    use NeoxDashBoard\NeoxDashBoardBundle\Enum\NeoxWidgetEnum;
    use NeoxDashBoard\NeoxDashBoardBundle\Enum\NeoxWidgetTypeEnum;
    use NeoxDashBoard\NeoxDashBoardBundle\Form\type\widgetType;
    use NeoxDashBoard\NeoxDashBoardBundle\Services\WidgetConfigService;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\EnumType;


    class NeoxDashWidgetType extends AbstractType
    {
        const TRANSPATH = 'neoxDashBoardWidget.form.';

        public function __construct(readonly WidgetConfigService $widgetConfigService)
        {
        }

        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            // Récupérer les widgets disponibles
            $widgets = $this->widgetConfigService->getWidgets();
            $choices = array_keys($widgets);


            $builder
                ->add('widget', EnumType::class, [ // Spécifiez explicitement ChoiceType
                     'class'              => NeoxWidgetEnum::class,
                     'required'           => true,
                     'label'              => $this->getTrans('widget'),
                     'translation_domain' => 'neoxDashBoardWidget',
                     'label_attr'         => [ 'class' => 'col-form-label text-start', ],
                     'attr'               => [
                         "placeholder" => $this->getTrans('widget', "placeholder"),
                         'class'       => 'form-control',
                     ],
                     'row_attr'           => [
                         'class' => 'row mb-3',
                     ],
                ])
                ->add('type', EnumType::class, array(
                    // 'disabled'      => true,
                    'class'              => NeoxWidgetTypeEnum::class,
                    'label'              => $this->getTrans('type'),
                    'translation_domain' => 'neoxDashBoardWidget',
                    'label_attr'         => [ 'class' => 'col-form-label text-start', ],
                    'required'           => false,
                    "attr"               => array(
                        "placeholder" => $this->getTrans('type', "placeholder"),
                        'class'       => 'required form-control '
                    ),
                    'row_attr'           => [ 'class' => 'row mb-3' ],
                ))
                ->add('url', textType::class, array(
                    // 'disabled'      => true,
                    'label'              => $this->getTrans('url'),
                    'translation_domain' => 'neoxDashBoardWidget',
                    'label_attr'         => [ 'class' => 'col-form-label text-start', ],
                    'required'           => false,
                    "attr"               => array(
                        "placeholder" => $this->getTrans('url', "placeholder"),
                        'class'       => 'required form-control '
                    ),
                    'row_attr'           => [ 'class' => 'row mb-3' ],
                ))
                ->add('options', TextareaType::class, array(
                    // 'disabled'      => true,
                    'label'              => $this->getTrans('options'),
                    'translation_domain' => 'neoxDashBoardWidget',
                    'label_attr'         => [ 'class' => 'col-form-label text-start', ],
                    'required'           => false,
                    "attr"               => array(
                        "placeholder" => $this->getTrans('options', "placeholder"),
                        'class'       => 'required form-control '
                    ),
                    'row_attr'           => [ 'class' => 'row mb-3' ],
                ))
            ;
        }

        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'data_class' => NeoxDashWidget::class,
            ]);
        }

        public function getTrans(string $field, string $type = "label"): string
        {
            return self::TRANSPATH . $field . "." . $type;
        }
    }
