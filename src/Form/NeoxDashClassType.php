<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Form;

    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashClass;
    use NeoxDashBoard\NeoxDashBoardBundle\Enum\NeoxDashTypeEnum;
    use NeoxDashBoard\NeoxDashBoardBundle\Enum\NeoxStyleEnum;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\EnumType;

    class NeoxDashClassType extends AbstractType
    {
        const TRANSPATH = 'neoxDashBoardClass.form.';

        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder->add('name', textType::class, [
                    'required'           => false,
                    'label'              => $this->getTrans('name'),
                    'translation_domain' => 'neoxDashBoardClass',
                    'label_attr'         => [ 'class' => 'col-form-label text-start', ],
                    'attr'               => [
                        "placeholder" => $this->getTrans('name', "placeholder"),
                        'class'       => 'form-control',
                    ],
                    'row_attr'           => [
                        'class' => 'row mb-3',
                    ],
                ])->add('type', EnumType::class, array(
                    // 'disabled'      => true,
                    'class'              => NeoxDashTypeEnum::class,
                    'label'              => $this->getTrans('type'),
                    'translation_domain' => 'neoxDashBoardClass',
                    'label_attr'         => [ 'class' => 'col-form-label text-start', ],
                    'required'           => false,
                    "attr"               => array(
                        "placeholder" => $this->getTrans('type', "placeholder"),
                        'class'       => 'required form-control '
                    ),

                    'row_attr' => [ 'class' => 'row mb-3' ],
                ))->add('mode', EnumType::class, array(
                    // 'disabled'      => true,
                    'class'              => NeoxStyleEnum::class,
                    'required'           => false,
                    'label'              => $this->getTrans('mode'),
                    'translation_domain' => 'neoxDashBoardClass',
                    'label_attr'         => [ 'class' => 'col-form-label text-start', ],
                    "attr" => array(
                        "placeholder" => $this->getTrans('mode', "placeholder"),
                        'class'       => 'required form-control '
                    ),

                    'row_attr' => [ 'class' => 'row mb-3' ],
                ));
        }

        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'data_class' => NeoxDashClass::class,
            ]);
        }

        public function getTrans(string $field, string $type = "label"): string
        {
            return self::TRANSPATH . $field . "." . $type;
        }
    }
