<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Twig\Components;

    use NeoxDashBoard\NeoxDashBoardBundle\Form\NeoxDashWidgetType;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Form\FormInterface;
    use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
    use Symfony\UX\LiveComponent\DefaultActionTrait;
    use Symfony\UX\LiveComponent\ComponentWithFormTrait;

    #[AsLiveComponent('NeoxDashWidget', template: '@NeoxDashBoardBundle/Components/NeoxDashWidget.html.twig')]
    class NeoxDashWidget extends AbstractController
    {
        use ComponentWithFormTrait;
        use DefaultActionTrait;

        /**
         * Instanciation du formulaire à utiliser dans le composant.
         */
        protected function instantiateForm(): FormInterface
        {
            return $this->createForm(NeoxDashWidgetType::class);
        }

        /**
         * Exemple de gestion d'une soumission du formulaire.
         */
        public function handleFormSubmission(array $data): void
        {
            // Logique pour traiter les données du formulaire après validation
            // Par exemple : Sauvegarde en base de données ou appel de service
            // $data contient les valeurs soumises
        }
    }
