<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Form;

    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashSetup;
    use NeoxDashBoard\NeoxDashBoardBundle\Enum\NeoxSearchEnum;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\EnumType;

    class NeoxDashSetupType extends AbstractType
    {
        const TRANSPATH = 'neoxDashBoardSetup.form.';

        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder->add('logo', textType::class, [
                'required'           => false,
                'label'              => $this->getTrans('logo'),
                'translation_domain' => 'neoxDashBoardSetup',
                'label_attr'         => [
                    'class'       => 'col-form-label text-start',
                    // Class du label
                ],
                'attr'               => [
                    "placeholder" => $this->getTrans('logo', "placeholder"),
                    'class' => 'form-control',
                    // Class du champ
                ],
                'row_attr'           => [
                    'class' => 'row mb-3',
                ],
            ])->add('home', textType::class, [
                'required'           => false,
                'label'              => $this->getTrans('main'),
                'translation_domain' => 'neoxDashBoardSetup',
                'label_attr'         => [

                    'class'       => 'col-form-label text-start',
                ],
                'attr'               => [
                    "placeholder" => $this->getTrans('main', "placeholder"),
                    'class' => 'form-control',
                ],
                'row_attr'           => [
                    'class' => 'row mb-3',
                ],
            ])->add('country', textType::class, [
                'required'           => true,
                'label'              => $this->getTrans('country'),
                'translation_domain' => 'neoxDashBoardSetup',
                'label_attr'         => [
                    'class'       => 'col-form-label text-start',
                ],
                'attr'               => [
                    "placeholder" => $this->getTrans('country', "placeholder"),
                    'class' => 'form-control',
                ],
                'row_attr'           => [
                    'class' => 'row mb-3',
                ],
            ])->add('weather', textType::class, array(
                // 'disabled'      => true,
                'required'           => false,
                "attr"               => array(
                    "placeholder" => $this->getTrans('weather', "placeholder"),
                    'class'       => 'required form-control'
                ),
                'label'              => $this->getTrans('weather'),
                'translation_domain' => 'neoxDashBoardSetup',
                'label_attr'         => [ 'class' => 'col-form-label text-start', ],
                'row_attr'           => [ 'class' => 'row mb-3' ],
            ))->add('search', EnumType::class, array(
                // 'disabled'      => true,
                'class'              => NeoxSearchEnum::class,
                'required'           => false,
                "attr"               => array(
                    "placeholder" => $this->getTrans('weather', "placeholder"),
                    'class'       => 'required form-control '
                ),
                'label'              => $this->getTrans('weather'),
                'translation_domain' => 'neoxDashBoardSetup',
                'label_attr'         => [ 'class' => 'col-form-label text-start', ],
                'row_attr'           => [ 'class' => 'row mb-3' ],
            ))->add('theme', textType::class, [
                'required'           => true,
                'label'              => $this->getTrans('theme'),
                'translation_domain' => 'neoxDashBoardSetup',
                'label_attr'         => [
                    'class'       => 'col-form-label text-start',
                ],
                'attr'               => [
                    "placeholder" => $this->getTrans('country', "placeholder"),
                    'class' => 'form-control',
                ],
                'row_attr'           => [
                    'class' => 'row mb-3',
                ],
            ]);
        }

        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'data_class' => NeoxDashSetup::class,
            ]);
        }

        public function getTrans(string $field, string $type = "label"): string
        {
            return self::TRANSPATH . $field . "." . $type;
        }
    }
