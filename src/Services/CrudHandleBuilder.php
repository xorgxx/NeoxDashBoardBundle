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
    use Twig\Environment;

    class CrudHandleBuilder
    {

        public ?IniHandleNeoxDashModel $iniHandleNeoxDashModel = null;

        public function __construct(readonly EntityManagerInterface $entityManager, readonly Environment $twig, readonly FormFactoryInterface $formFactory, readonly RouterInterface $router)
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
        public function setHandleNeoxDashModel(iniHandleNeoxDashModel $IniHandleNeoxDashModel): self|FormInterface
        {
            $this->iniHandleNeoxDashModel = $IniHandleNeoxDashModel;
            return $this;
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
        public function handleCreateForm( bool $raw = false): self|array
        {
            // build action for form
            $route  = $this->iniHandleNeoxDashModel->getRoute();
            $params = $this->iniHandleNeoxDashModel->getEntity()->getId() ? [ 'id' => $this->iniHandleNeoxDashModel->getEntity()->getId() ] : $this->iniHandleNeoxDashModel->getParams();
            $action = $this->router->generate("{$route}_" . ($this->iniHandleNeoxDashModel->getEntity()->getId() ? 'edit' : 'new'), $params);


            // Create the form generically
            // Return the form if it is invalid or not submitted
            $formInterface = $this->formFactory->create($this->iniHandleNeoxDashModel->getFormInterface(), $this->iniHandleNeoxDashModel->getEntity(), [
                'action' => $action, 'method' => 'POST',
            ]);

            $this->iniHandleNeoxDashModel->setFormInterface($formInterface);

            if ($raw) {
                return $formInterface;
            }else{
                return $this;
            }
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
        public function handleForm($request, bool $raw = false): self|array
        {

            // Merge form
            $this->iniHandleNeoxDashModel->getFormInterface()->handleRequest($request);

            // identification type request
            $return = $this->getRequestType($request);

            // submit form
            if ($this->iniHandleNeoxDashModel->getFormInterface()->isSubmitted() && $this->iniHandleNeoxDashModel->getFormInterface()->isValid()) {
                $this->entityManager->persist($this->iniHandleNeoxDashModel->getEntity());
                $this->entityManager->flush();

                $return[ "submit" ] = true;

                // If the query does not match any of the previous cases ("unmatch")
                // Code 400 pour requête incorrecte
                //   $return["status"]   = "unmatch";
                //   $return["data"]     = $this->getJsonResponse('Query type not supported', response::HTTP_BAD_REQUEST);
            }
            $return[ "formType" ] = $this->iniHandleNeoxDashModel->getFormInterface();
            // Return the form if it is invalid or not submitted
            $this->iniHandleNeoxDashModel->setReturn($return);

            if ($raw) {
                return $return;
            }else{
                return $this;
            }


        }

        public function render()
        {
            $return = $this->iniHandleNeoxDashModel->getReturn();
            
            // This will render only what we need to send
            return match ($return[ "status" ]) {
                "redirect"  => $return[ "submit" ] ? $this->redirectToRoute($this->getIniHandleNeoxDashModel()->getRoute() . '_index') : null,
                "ajax"      => $return[ "submit" ] ? new JsonResponse(true) : new Response($this->twig->render($this->getIniHandleNeoxDashModel()->getForm(), ['form' => $return[ "formType" ]->createView(), ])),
                "turbo"     => $return[ "submit" ] ? $return[ "data" ] : new Response($this->twig->render($this->getIniHandleNeoxDashModel()->getNew(), [ 'form' => $return[ "formType" ]->createView(), ])),
                default     => new Response($this->twig->render($this->getIniHandleNeoxDashModel()->getNew(), [ 'form' => $return[ "formType" ]->createView(), ])),
            };
        }

        /**
         * @param       $request
         * @param array $return
         *
         * @return array
         */
        public function getRequestType($request): array
        {
            return [
                "submit"    => false, 
                "data"      => $this->iniHandleNeoxDashModel->getFormInterface() ?? null, 
                "status"    => match (true) {
                    $request->isXmlHttpRequest()    => 'ajax',                                          // AJAX request
                    TurboBundle::STREAM_FORMAT      === $request->getPreferredFormat() => 'turbo',      // Turbo Stream request
                    $request->isMethod('POST')      => 'redirect',                                      // Classic POST request
                    default                         => 'standard',                                      // Classic request (GET, etc.)
                }
            ];
        }


    }
