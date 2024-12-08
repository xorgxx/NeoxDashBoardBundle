<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Services;

    use Doctrine\ORM\EntityManagerInterface;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashSetup;
    use NeoxDashBoard\NeoxDashBoardBundle\Pattern\IniHandleNeoxDashModel;
    use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashSetupRepository;
    use Symfony\Bundle\SecurityBundle\Security;
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

        public function __construct(
            readonly EntityManagerInterface $entityManager, 
            readonly Environment $twig, 
            readonly FormFactoryInterface $formFactory, 
            readonly RouterInterface $router,
            readonly Security $security,
            readonly NeoxDashSetupRepository $neoxDashSetupRepository
        )
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

        /**
         * Handle form submission for any entity and form type
         *
         * @param               $request
         * @param FormInterface $form
         * @param object        $entity
         *
         * @return mixed
         */
        public function preHandleForm($request): self
        {

            // Merge form
            $this->iniHandleNeoxDashModel->getFormInterface()->handleRequest($request);

            // identification type request
            $return = $this->getRequestType($request);

            // submit form
            if ($this->iniHandleNeoxDashModel->getFormInterface()->isSubmitted() && $this->iniHandleNeoxDashModel->getFormInterface()->isValid()) {
                $return[ "submit" ] = true;
            }
            $return[ "formType" ] = $this->iniHandleNeoxDashModel->getFormInterface();
            // Return the form if it is invalid or not submitted
            $this->iniHandleNeoxDashModel->setReturn($return);

            return $this;
        }

        public function flushHandleForm(): self
        {
            if($this->iniHandleNeoxDashModel->getReturn()["submit"]) {
                $this->entityManager->persist($this->iniHandleNeoxDashModel->getEntity());
                $this->entityManager->flush();
            }

            return $this;
        }


        public function render()
        {
            $return = $this->iniHandleNeoxDashModel->getReturn();
            
            // This will render only what we need to send
            return match ($return[ "status" ]) {
                "redirect"  => $return[ "submit" ] ? $this->redirectToRoute($this->getIniHandleNeoxDashModel()->getRoute() . '_index') : null,
                "ajax"      => $return[ "submit" ] ? new JsonResponse($return[ "data" ] ?? true) : new Response($this->twig->render($this->getIniHandleNeoxDashModel()->getForm(), ['form' => $return[ "formType" ]->createView(), ])),
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

        public function getNeoxDasSetup(): NeoxDashSetup
        {
            // Vérifier si l'utilisateur est connecté et s'il a un setup
            $userSetup = $this->security->getUser()?->getNeoxDashSetup();

            // Si l'utilisateur a un setup, on le retourne
            if ($userSetup) {
                return $userSetup;
            }

            // Si l'utilisateur n'a pas de setup, vérifier s'il y a un setup par défaut en base
            $defaultSetup = $this->neoxDashSetupRepository->findOneBy(['id' => 1]);

            // Si un setup par défaut existe en base, on le retourne
            if ($defaultSetup) {
                return $defaultSetup;
            }

            // Si aucun setup n'est trouvé, lancer une exception ou retourner un setup par défaut
            throw $this->createNotFoundException('No NeoxDashSetup found for this user or as default.');
        }

    }
