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
            $setup = [
                // full html form
                "new"   => '@NeoxDashBoardBundle/neox_dash_class/new.html.twig',
                // only form
                "_form" => '@NeoxDashBoardBundle/neox_dash_class/_form.html.twig',
                // name route without _index | _new ....
                "route" => 'app_neox_dash_class'
            ];
            // build entity
            $neoxDashClass = new NeoxDashClass();

            // build Form entity Generic
            $form = $this->formHandlerService->handleCreateForm($neoxDashClass, NeoxDashClassType::class, $setup);

            // Build form
            $form->handleRequest($request);

            /*
             * Call to the generic form management service, with support for turbo-stream
             * For kipping this code flexible to return your need
             */
            [$return, $form]  = $this->formHandlerService->handleForm($request, $form, $neoxDashClass, $setup);
            return match ($return[ "status" ]) {
                "redirect"  => $return[ "submit" ] ? $this->redirectToRoute($setup["route"] . '_index') : null,
                "ajax"      => $return[ "submit" ] ? "ok" : $this->render($setup["_form"], [
                    'form' => $form->createView(),
                ]),
                "turbo"     => $return[ "submit" ] ? $return[ "data" ] : $this->render($setup["new"], [
                    'form' => $form->createView(),
                ]),
                default     => $this->render($setup["new"], [ 'form' => $form->createView(), ]),
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
            $setup = [
                // full html form
                "new"   => '@NeoxDashBoardBundle/neox_dash_class/edit.html.twig',
                // only form
                "_form" => '@NeoxDashBoardBundle/neox_dash_class/_form.html.twig',
                // name route without _index | _new ....
                "route" => 'app_neox_dash_class'
            ];

            // build Form entity Generic
            $form = $this->formHandlerService->handleCreateForm($neoxDashClass, NeoxDashClassType::class, $setup);

            // Merge form
            $form->handleRequest($request);

            /*
             * Call to the generic form management service, with support for turbo-stream
             * For kipping this code flexible to return your need
             */
            [$return, $form] = $this->formHandlerService->handleForm($request, $form, $neoxDashClass, $setup);
            return match ($return[ "status" ]) {
                "redirect"  => $return[ "submit" ] ? $this->redirectToRoute($setup["route"] . '_index') : null,
                "ajax"      => $return[ "submit" ] ? new JsonResponse(true) : $this->render($setup["_form"], [
                    'form' => $form->createView(),
                ]),
                "turbo"     => $return[ "submit" ] ? $return[ "data" ] : $this->render($setup["new"], [
                    'form' => $form->createView(),
                ]),
                default     => $this->render($setup["new"], [ 'form' => $form->createView(), ]),
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
    }
