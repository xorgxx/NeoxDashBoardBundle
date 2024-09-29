<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Services;

    use Doctrine\ORM\EntityManagerInterface;
    use NeoxDashBoard\NeoxDashBoardBundle\Pattern\IniHandleNeoxDashModel;
    use Symfony\Component\Form\FormFactoryInterface;
    use Symfony\Component\Form\FormInterface;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\RouterInterface;
    use Symfony\UX\Turbo\TurboBundle;

    class FormHandlerService
    {

        public ?IniHandleNeoxDashModel $iniHandleNeoxDashModel = null;


        public function __construct(readonly EntityManagerInterface $entityManager, readonly FormFactoryInterface $formFactory, readonly RouterInterface $router)
        {
        }


        // Retourne l'instance actuelle de iniHandleNeoxDashModel
        public function getIniHandleNeoxDashModel(): ?iniHandleNeoxDashModel
        {
            return $this->iniHandleNeoxDashModel;
        }

        // Crée et retourne une nouvelle instance de iniHandleNeoxDashModel
        public function createNewHandleNeoxDashModel(): iniHandleNeoxDashModel
        {
            $this->iniHandleNeoxDashModel = new iniHandleNeoxDashModel();  // Crée et stocke la nouvelle instance
            return $this->iniHandleNeoxDashModel;
        }

        // Définis une instance de iniHandleNeoxDashModel dans la propriété
        public function setHandleNeoxDashModel(iniHandleNeoxDashModel $IniHandleNeoxDashModel): self
        {
            $this->iniHandleNeoxDashModel = $IniHandleNeoxDashModel;
            return $this;
        }

        /**
         * Handle form submission for any entity and form type
         *
         * @param               $request
         * @param FormInterface $form
         * @param object        $entity
         *
         * @return mixed
         */
        public function handleForm($request, FormInterface $form, object $entity): array
        {

            // identification type request
            $return = $this->getRequestType($request, $form);

            // submit form
            if ($form->isSubmitted() && $form->isValid()) {
                $this->entityManager->persist($entity);
                $this->entityManager->flush();

                $return[ "submit" ] = true;

                // If the query does not match any of the previous cases ("unmatch")
                // Code 400 pour requête incorrecte
                //   $return["status"]   = "unmatch";
                //   $return["data"]     = $this->getJsonResponse('Query type not supported', response::HTTP_BAD_REQUEST);
            }

            // Return the form if it is invalid or not submitted
            return [
                $return, $form
            ];
        }


        /**
         * Handle form creation for any entity and form type
         *
         * @param object $entity
         * @param string $formType
         * @param array  $setup
         *
         * @return mixed
         */
        public function handleCreateForm(object $entity, string $formType): FormInterface
        {
            // build action for form
            $route  = $this->iniHandleNeoxDashModel->getRoute();
            $params = $entity->getId() ? [ 'id' => $entity->getId() ] : $this->iniHandleNeoxDashModel->getParams();
            $action = $this->router->generate("{$route}_" . ($entity->getId() ? 'edit' : 'new'), $params);


            // Create the form generically
            // Return the form if it is invalid or not submitted
            return $this->formFactory->create($formType, $entity, [
                'action' => $action, 'method' => 'POST',
            ]);
        }

        /**
         * @param       $request
         * @param array $return
         *
         * @return array
         */
        public function getRequestType($request, ?FormInterface $form = null): array
        {
            return [
                "submit" => false, "data" => $form, "status" => match (true) {
                    $request->isXmlHttpRequest() => 'ajax',                                     // AJAX request
                    TurboBundle::STREAM_FORMAT === $request->getPreferredFormat() => 'turbo',   // Turbo Stream request
                    $request->isMethod('POST') => 'redirect',                                   // Classic POST request
                    default => 'standard',                                                      // Classic request (GET, etc.)
                }
            ];
        }


    }
