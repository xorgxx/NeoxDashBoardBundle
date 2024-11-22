<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Form;

    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashFavorite;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

    class NeoxDashFavoriteType extends AbstractType
    {
        const TRANSPATH = 'neoxDashBoardFavorite.form.';

        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder->add('Favorite', CheckBoxType::class, array(
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
            ;
        }

        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'data_class' => NeoxDashFavorite::class,
            ]);
        }

        public function getTrans(string $field, string $type = "label"): string
        {
            return self::TRANSPATH . $field . "." . $type;
        }
    }
