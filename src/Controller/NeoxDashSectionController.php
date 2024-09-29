<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Controller;

    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashClass;
    use NeoxDashBoard\NeoxDashBoardBundle\Services\FormHandlerService;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashSection;
    use NeoxDashBoard\NeoxDashBoardBundle\Form\NeoxDashSectionType;
    use Doctrine\ORM\EntityManagerInterface;
    use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashSectionRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Attribute\Route;
    use Symfony\UX\Turbo\TurboBundle;

    #[Route('/neox/dash/section')]
    final class NeoxDashSectionController extends AbstractController
    {

        public function __construct(readonly private FormHandlerService $formHandlerService)
        {
        }

        #[Route('/', name: 'app_neox_dash_section_index', methods: [ 'GET' ])]
        public function index(NeoxDashSectionRepository $neoxDashSectionRepository): Response
        {
            return $this->render('@NeoxDashBoardBundle/neox_dash_section/index.html.twig', [ 'neox_dash_sections' => $neoxDashSectionRepository->findAll(), ]);
        }

        #[Route('/new/{id}', name: 'app_neox_dash_section_new', methods: [
            'GET', 'POST'
        ])]
        public function new(Request $request, NeoxDashClass $neoxDashClass): Response|JsonResponse
        {
            $formHandlerService = $this->setInit("new", [ 'id' => $neoxDashClass->getId() ]);

            $neoxDashSection = new NeoxDashSection();
            $neoxDashSection->setClass($neoxDashClass);

            // build Form entity Generic
            $form = $formHandlerService->handleCreateForm($neoxDashSection, NeoxDashSectionType::class);

            // Merge form
            $form->handleRequest($request);

            /*
             * Call to the generic form management service, with support for turbo-stream
             * For kipping this code flexible to return your need
             */
            [ $return, $form ] = $formHandlerService->handleForm($request, $form, $neoxDashSection);

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

        #[Route('/{id}', name: 'app_neox_dash_section_show', methods: [ 'GET' ])]
        public function show(NeoxDashSection $neoxDashSection): Response
        {
            return $this->render('@NeoxDashBoardBundle/neox_dash_section/show.html.twig', [ 'neox_dash_section' => $neoxDashSection, ]);
        }


        #[Route('/{id}/edit', name: 'app_neox_dash_section_edit', methods: [
            'GET', 'POST'
        ])]
        public function edit(Request $request, NeoxDashSection $neoxDashSection): Response|JsonResponse
        {
            $formHandlerService = $this->setInit("edit");

            // build Form entity Generic
            $form = $formHandlerService->handleCreateForm($neoxDashSection, NeoxDashSectionType::class);

            // Merge form
            $form->handleRequest($request);

            /*
             * Call to the generic form management service, with support for turbo-stream
             * For kipping this code flexible to return your need
             */
            [ $return, $form ] = $formHandlerService->handleForm($request, $form, $neoxDashSection);

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


        #[Route('/{id}', name: 'app_neox_dash_section_delete', methods: [ 'POST' ])]
        public function delete(Request $request, NeoxDashSection $neoxDashSection, EntityManagerInterface $entityManager): Response
        {
            if ($this->isCsrfTokenValid('delete' . $neoxDashSection->getId(), $request
                ->getPayload()
                ->getString('_token'))) {
                $entityManager->remove($neoxDashSection);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_neox_dash_section_index', [], Response::HTTP_SEE_OTHER);
        }

        /**
         * @return IniHandleNeoxDashModel
         */
        public function setInit(string $name = "new", array $params = []): FormHandlerService
        {
            $o = $this->formHandlerService
                ->createNewHandleNeoxDashModel()
                ->setNew("@NeoxDashBoardBundle/neox_dash_section/$name.html.twig")
                ->setForm('@NeoxDashBoardBundle/neox_dash_section/_form.html.twig')
                ->setRoute('app_neox_dash_section')
                ->setParams($params)
            ;

            // Determine the template to use for rendering
            return $this->formHandlerService->setHandleNeoxDashModel($o);
        }
    }
