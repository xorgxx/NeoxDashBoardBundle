<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Controller;

    use NeoxDashBoard\NeoxDashBoardBundle\Services\FormHandlerService;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashSetup;
    use NeoxDashBoard\NeoxDashBoardBundle\Form\NeoxDashSetupType;
    use Doctrine\ORM\EntityManagerInterface;
    use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashSetupRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Attribute\Route;
    use Symfony\UX\Turbo\TurboBundle;

    #[Route('/neox/dash/setup')]
    final class NeoxDashSetupController extends AbstractController
    {

        public function __construct(readonly private FormHandlerService $formHandlerService)
        {
        }

        #[Route('/', name: 'app_neox_dash_setup_index', methods: [ 'GET' ])]
        public function index(NeoxDashSetupRepository $neoxDashSetupRepository): Response
        {
            return $this->render('@NeoxDashBoardBundle/neox_dash_setup/index.html.twig', [ 'neox_dash_setups' => $neoxDashSetupRepository->findAll(), ]);
        }

        #[Route('/new', name: 'app_neox_dash_setup_new', methods: [ 'GET', 'POST' ])]
        public function new(Request $request): Response | JsonResponse
        {
            // Determine the template to use for rendering
            $setup = [
                // full html form
                "new"   => '@NeoxDashBoardBundle/neox_dash_setup/new.html.twig',
                // only form
                "_form" => '@NeoxDashBoardBundle/neox_dash_setup/_form.html.twig',
                // name route without _index | _new ....
                "route" => 'app_neox_dash_setup'
            ];
            // build entity
            $neoxDashSetup = new NeoxDashSetup();

            // build Form entity Generic
            $form = $this->formHandlerService->handleCreateForm($neoxDashSetup, NeoxDashSetupType::class, $setup);

            // Build form
            $form->handleRequest($request);

            /*
             * Call to the generic form management service, with support for turbo-stream
             * For kipping this code flexible to return your need
             */
            [$return, $form]  = $this->formHandlerService->handleForm($request, $form, $neoxDashSetup, $setup);
            return match ($return[ "status" ]) {
                "redirect"  => $return[ "submit" ] ? $this->redirectToRoute($setup["route"] . '_index') : null,
                "ajax"      => $return[ "submit" ] ? new JsonResponse(true) : $this->render($setup["_form"], [
                    'form' => $form->createView(),
                ]),
//                "ajax" => $return["submit"]
//                    ? new JsonResponse(["status" => "ok"]) // Example response for AJAX
//                    : $this->render($setup["_form"], [
//                        'form' => $form->createView(),
//                    ]),
                "turbo"     => $return[ "submit" ] ? $return[ "data" ] : $this->render($setup["new"], [
                    'form' => $form->createView(),
                ]),
                default     => $this->render($setup["new"], [ 'form' => $form->createView(), ]),
            };

        }

        #[Route('/{id}', name: 'app_neox_dash_setup_show', methods: [ 'GET' ])]
        public function show(NeoxDashSetup $neoxDashSetup): Response
        {
            return $this->render('@NeoxDashBoardBundle/neox_dash_setup/show.html.twig', [ 'neox_dash_setup' => $neoxDashSetup, ]);
        }

        #[Route('/{id}/edit', name: 'app_neox_dash_setup_edit', methods: [
            'GET', 'POST'
        ])]
        public function edit(Request $request, NeoxDashSetup $neoxDashSetup): Response | JsonResponse
        {
            
            // Determine the template to use for rendering
            $setup = [
                // full html form
                "new"   => '@NeoxDashBoardBundle/neox_dash_setup/edit.html.twig',
                // only form
                "_form" => '@NeoxDashBoardBundle/neox_dash_setup/_form.html.twig',
                // name route without _index | _new ....
                "route" => 'app_neox_dash_setup'
            ];

            // build Form entity Generic
            $form = $this->formHandlerService->handleCreateForm($neoxDashSetup, NeoxDashSetupType::class, $setup);

            // Merge form
            $form->handleRequest($request);

            /*
             * Call to the generic form management service, with support for turbo-stream
             * For kipping this code flexible to return your need
             */
            [$return, $form] = $this->formHandlerService->handleForm($request, $form, $neoxDashSetup, $setup);
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

        #[Route('/{id}', name: 'app_neox_dash_setup_delete', methods: [ 'POST' ])]
        public function delete(Request $request, NeoxDashSetup $neoxDashSetup, EntityManagerInterface $entityManager): Response
        {
            if ($this->isCsrfTokenValid('delete' . $neoxDashSetup->getId(), $request->getPayload()->getString('_token'))) {
                $entityManager->remove($neoxDashSetup);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_neox_dash_setup_index', [], Response::HTTP_SEE_OTHER);
        }
    }
