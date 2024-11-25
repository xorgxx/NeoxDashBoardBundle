<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Form;

    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashSection;
    use NeoxDashBoard\NeoxDashBoardBundle\Enum\NeoxSizeEnum;
    use Symfony\Component\Form\Extension\Core\Type\Integer;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\IntegerType;
    use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
    use Symfony\Component\Form\Extension\Core\Type\NumberType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\EnumType;
    use Symfony\Component\Form\Extension\Core\Type\ColorType;

    class NeoxDashSectionType extends AbstractType
    {
        const TRANSPATH = 'neoxDashBoardSection.form.';

        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                ->add('name', textType::class, [
                    'required'           => true,
                    'label'              => $this->getTrans('name'),
                    'translation_domain' => 'neoxDashBoardSection',
                    'label_attr'         => [
                        "placeholder" => $this->getTrans('name', "placeholder"),
                        'class'       => 'col-form-label text-start',
                    ],
                    'attr'               => [
                        'class' => 'form-control',
                    ],
                    'row_attr'           => [
                        'class' => 'row mb-3',
                    ],
                ])
                ->add('row', IntegerType::class, array(
                    // 'disabled'      => true,
                    'required'           => false,
                    "attr"               => array(
                        "placeholder" => $this->getTrans('row', "placeholder"),
                        'class'       => 'required form-control '
                    ),
                    'label'              => $this->getTrans('row'),
                    'translation_domain' => 'neoxDashBoardSection',
                    'label_attr'         => [ 'class' => 'col-form-label text-start', ],
                    'row_attr'           => [ 'class' => 'row mb-3' ],
                ))
                ->add('size', EnumType::class, array(
                    // 'disabled'      => true,
                    'class'              => NeoxSizeEnum::class,
                    // 'disabled'      => true,
                    'required'           => false,
                    "attr"               => array(
                        "placeholder" => $this->getTrans('size', "placeholder"),
                        'class'       => 'required form-control '
                    ),
                    'label'              => $this->getTrans('size'),
                    'translation_domain' => 'neoxDashBoardSection',
                    'label_attr'         => [ 'class' => 'col-form-label text-start', ],
                    'row_attr'           => [ 'class' => 'row mb-3' ],
                ))
                ->add('content', CheckBoxType::class, array(
                    // 'disabled'      => true,
                    'required'           => false,
                    "attr"               => array(
                        "placeholder" => $this->getTrans('content', "placeholder"),
                        'class'       => 'required form-control '
                    ),
                    'label'              => $this->getTrans('content'),
                    'translation_domain' => 'neoxDashBoardSection',
                    'label_attr'         => [ 'class' => 'col-form-label text-start', ],
                    'row_attr'           => [ 'class' => 'row mb-3' ],
                ))
                ->add('height', NumberType::class, array(
                    // 'disabled'      => true,
                    'required'           => false,
                    "attr"               => array(
                        "placeholder" => $this->getTrans('height', "placeholder"),
                        'class'       => 'required form-control '
                    ),
                    'label'              => $this->getTrans('height'),
                    'translation_domain' => 'neoxDashBoardSection',
                    'label_attr'         => [ 'class' => 'col-form-label text-start', ],
                    'row_attr'           => [ 'class' => 'row mb-3' ],
                ))
                ->add('timer', IntegerType::class, array(
                    // 'disabled'      => true,
                    'required'           => false,
                    "attr"               => array(
                        "placeholder" => $this->getTrans('timer', "placeholder"),
                        'class'       => 'required form-control '
                    ),
                    'label'              => $this->getTrans('timer'),
                    'translation_domain' => 'neoxDashBoardSection',
                    'label_attr'         => [ 'class' => 'col-form-label text-start', ],
                    'row_attr'           => [ 'class' => 'row mb-3' ],
                ))->add('headerColor', ColorType::class, [
                    'required'           => false,
                    'label'              => $this->getTrans('color'),
                    'translation_domain' => 'neoxDashBoardDomain',
                    'label_attr'         => [ 'class' => 'col-form-label text-start', ],
                    'attr'               => [
                        "placeholder" => $this->getTrans('color', "placeholder"),
                        'class'       => 'form-control',
                    ],
                    'row_attr'           => [
                        'class' => 'row mb-3',
                    ],
                ]);
        }

        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'data_class' => NeoxDashSection::class,
            ]);
        }

        public function getTrans(string $field, string $type = "label"): string
        {
            return self::TRANSPATH . $field . "." . $type;
        }
    }
