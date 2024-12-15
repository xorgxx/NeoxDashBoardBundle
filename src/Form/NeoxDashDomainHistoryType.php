<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Form;

    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashDomainHistory;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\IntegerType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;


    class NeoxDashDomainHistoryType extends AbstractType
    {
        const TRANSPATH = 'neoxDashBoardDomainHistory.form.';

        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder->add('count', IntegerType::class, [
                    'required'           => true,
                    'label'              => $this->getTrans('count'),
                    'translation_domain' => 'neoxDashBoardDomainHistory',
                    'label_attr'         => [
                        "placeholder" => $this->getTrans('count', "placeholder"),
                        'class'       => 'col-form-label text-start',
                    ],
                    'attr'               => [
                        'class' => 'form-control',
                    ],
                    'row_attr'           => [
                        'class' => 'row mb-3',
                    ],
                ])
            ;
        }

        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'data_class' => NeoxDashDomainHistory::class,
            ]);
        }

        public function getTrans(string $field, string $type = "label"): string
        {
            return self::TRANSPATH . $field . "." . $type;
        }
    }
