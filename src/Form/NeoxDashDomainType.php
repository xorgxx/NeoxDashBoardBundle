<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Form;

    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashDomain;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashFavorite;
    use Symfony\Bridge\Doctrine\Form\Type\EntityType;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\EnumType;
    use Symfony\Component\Form\Extension\Core\Type\ColorType;
    use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
    use Symfony\Component\Form\Extension\Core\Type\CollectionType;
    use Symfony\Component\Form\Extension\Core\Type\FormType;

    class NeoxDashDomainType extends AbstractType
    {
        const TRANSPATH = 'neoxDashBoardDomain.form.';

        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder->add('name', textType::class, [
                    'required'           => true,
                    'label'              => $this->getTrans('name'),
                    'translation_domain' => 'neoxDashBoardDomain',
                    'label_attr'         => [ 'class' => 'col-form-label text-start', ],
                    'attr'               => [
                        "placeholder" => $this->getTrans('name', "placeholder"),
                        'class'       => 'form-control',
                    ],
                    'row_attr'           => [
                        'class' => 'row mb-3',
                    ],
                ])->add('url', textType::class, [
                'required'           => true,
                'label'              => $this->getTrans('url'),
                'translation_domain' => 'neoxDashBoardDomain',
                'label_attr'         => [ 'class' => 'col-form-label text-start', ],
                'attr'               => [
                    "placeholder" => $this->getTrans('url', "placeholder"),
                    'class'       => 'form-control',
                ],
                'row_attr'           => [
                    'class' => 'row mb-3',
                ],
            ])->add('content', textType::class, [
                'required'           => false,
                'label'              => $this->getTrans('url'),
                'translation_domain' => 'neoxDashBoardDomain',
                'label_attr'         => [ 'class' => 'col-form-label text-start', ],
                'attr'               => [
                    "placeholder" => $this->getTrans('url', "placeholder"),
                    'class'       => 'form-control',
                ],
                'row_attr'           => [
                    'class' => 'row mb-3',
                ],
            ])->add('color', ColorType::class, [
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
            ])
//              ->add('favorite',  FormType::class, [
//                'data_class' => neoxDashFavorite::class,
//                ]
//            )
            ;
        }

        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'data_class' => NeoxDashDomain::class,
            ]);
        }

        public function getTrans(string $field, string $type = "label"): string
        {
            return self::TRANSPATH . $field . "." . $type;
        }
    }
