<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Controller;

    use NeoxDashBoard\NeoxDashBoardBundle\Services\FormHandlerService;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashClass;
    use NeoxDashBoard\NeoxDashBoardBundle\Form\NeoxDashClassType;
    use Doctrine\ORM\EntityManagerInterface;
    use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashClassRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Attribute\Route;
    use Symfony\UX\Turbo\TurboBundle;

    #[Route('/neox/dash/class')]
    final class NeoxDashClassController extends AbstractController
    {

        public function __construct(readonly private FormHandlerService $formHandlerService)
        {
        }

        #[Route('/', name: 'app_neox_dash_class_index', methods: [ 'GET' ])]
        public function index(NeoxDashClassRepository $neoxDashClassRepository): Response
        {
            return $this->render('@NeoxDashBoardBundle/neox_dash_class/index.html.twig', [ 'neox_dash_classs' => $neoxDashClassRepository->findAll(), ]);
        }

        #[Route('/new', name: 'app_neox_dash_class_new', methods: [ 'GET', 'POST' ])]
        public function new(Request $request): Response | JsonResponse
        {
            // Determine the template to use for rendering
            $formHandlerService = $this->setInit("new");
            // build Form entity Generic
            $form               = $formHandlerService->handleCreateForm($neoxDashClass, NeoxDashClassType::class);

            // Merge form
            $form->handleRequest($request);

            /*
             * Call to the generic form management service, with support for turbo-stream
             * For kipping this code flexible to return your need
             */
            [$return, $form]    = $formHandlerService->handleForm($request, $form, $neoxDashClass);
            return match ($return[ "status" ]) {
                "redirect"  => $return[ "submit" ] ? $this->redirectToRoute($formHandlerService->getIniHandleNeoxDashModel()->getRoute() . '_index') : null,
                "ajax"      => $return[ "submit" ] ? new JsonResponse(true): $this->render($formHandlerService->getIniHandleNeoxDashModel()->getForm(), [
                    'form' => $form->createView(),
                ]),
                "turbo"     => $return[ "submit" ] ? $return[ "data" ] : $this->render($formHandlerService->getIniHandleNeoxDashModel()->getNew(), [
                    'form' => $form->createView(),
                ]),
                default     => $this->render($formHandlerService->getIniHandleNeoxDashModel()->getNew(), [ 'form' => $form->createView(), ]),
            };

        }

        #[Route('/{id}', name: 'app_neox_dash_class_show', methods: [ 'GET' ])]
        public function show(NeoxDashClass $neoxDashClass): Response
        {
            return $this->render('@NeoxDashBoardBundle/neox_dash_class/show.html.twig', [ 'neox_dash_class' => $neoxDashClass, ]);
        }

        #[Route('/{id}/edit', name: 'app_neox_dash_class_edit', methods: [
            'GET', 'POST'
        ])]
        public function edit(Request $request, NeoxDashClass $neoxDashClass): Response | JsonResponse
        {            
            // Determine the template to use for rendering
            $formHandlerService = $this->setInit("edit");
            // build Form entity Generic
            $form               = $formHandlerService->handleCreateForm($neoxDashClass, NeoxDashClassType::class);

            // Merge form
            $form->handleRequest($request);

            /*
             * Call to the generic form management service, with support for turbo-stream
             * For kipping this code flexible to return your need
             */
            [$return, $form]    = $formHandlerService->handleForm($request, $form, $neoxDashClass);
            return match ($return[ "status" ]) {
                "redirect"  => $return[ "submit" ] ? $this->redirectToRoute($formHandlerService->getIniHandleNeoxDashModel()->getRoute() . '_index') : null,
                "ajax"      => $return[ "submit" ] ? new JsonResponse(true): $this->render($formHandlerService->getIniHandleNeoxDashModel()->getForm(), [
                    'form' => $form->createView(),
                ]),
                "turbo"     => $return[ "submit" ] ? $return[ "data" ] : $this->render($formHandlerService->getIniHandleNeoxDashModel()->getNew(), [
                    'form' => $form->createView(),
                ]),
                default     => $this->render($formHandlerService->getIniHandleNeoxDashModel()->getNew(), [ 'form' => $form->createView(), ]),
            };
        }

        #[Route('/{id}', name: 'app_neox_dash_class_delete', methods: [ 'POST' ])]
        public function delete(Request $request, NeoxDashClass $neoxDashClass, EntityManagerInterface $entityManager): Response
        {
            if ($this->isCsrfTokenValid('delete' . $neoxDashClass->getId(), $request->getPayload()->getString('_token'))) {
                $entityManager->remove($neoxDashClass);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_neox_dash_class_index', [], Response::HTTP_SEE_OTHER);
        }

        /**
         * @return IniHandleNeoxDashModel
         */
        public function setInit(string $name = "new", array $params = []):  FormHandlerService
        {
            $o = $this->formHandlerService->createNewHandleNeoxDashModel()
                ->setNew("@NeoxDashBoardBundle/neox_dash_class/$name.html.twig")
                ->setForm('@NeoxDashBoardBundle/neox_dash_class/_form.html.twig')
                ->setRoute('app_neox_dash_class')
                ->setParams($params)
            ;

            // Determine the template to use for rendering
            return $this->formHandlerService->setHandleNeoxDashModel($o);
        }
    }
