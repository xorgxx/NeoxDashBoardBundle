<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Form;

    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashSection;
    use Symfony\Component\Form\Extension\Core\Type\Integer;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\IntegerType;
    use Symfony\Component\Form\Extension\Core\Type\NumberType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\EnumType;

    class NeoxDashSectionType extends AbstractType
    {
        const TRANSPATH = 'neoxDashBoardSection.form.';

        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder->add('name', textType::class, [
                    'required'   => true,
                    'label'              => $this->getTrans('name'),
                    'translation_domain' => 'neoxDashBoardSection',
                    'label_attr' => [
                        "placeholder" => $this->getTrans('name', "placeholder"),
                        'class' => 'col-form-label text-start',
                    ],
                    'attr'       => [
                        'class' => 'form-control',
                    ],
                    'row_attr'   => [
                        'class' => 'row mb-3',
                    ],
                ])->add('row', IntegerType::class, array(
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
                ))->add('colonne', IntegerType::class, array(
                    // 'disabled'      => true,
                    'required'           => false,
                    "attr"               => array(
                        "placeholder" => $this->getTrans('colonne', "placeholder"),
                        'class'       => 'required form-control '
                    ),
                    'label'              => $this->getTrans('colonne'),
                    'translation_domain' => 'neoxDashBoardSection',
                    'label_attr'         => [ 'class' => 'col-form-label text-start', ],
                    'row_attr'           => [ 'class' => 'row mb-3' ],
                ))->add('heigth', NumberType::class, array(
                // 'disabled'      => true,
                'required'           => false,
                "attr"               => array(
                    "placeholder" => $this->getTrans('heigth', "placeholder"),
                    'class'       => 'required form-control '
                ),
                'label'              => $this->getTrans('heigth'),
                'translation_domain' => 'neoxDashBoardSection',
                'label_attr'         => [ 'class' => 'col-form-label text-start', ],
                'row_attr'           => [ 'class' => 'row mb-3' ],
            ))->add('timer', IntegerType::class, array(
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
                ));
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
