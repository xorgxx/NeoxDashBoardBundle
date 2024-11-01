<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Controller;

    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashClass;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashDomain;
    use NeoxDashBoard\NeoxDashBoardBundle\Pattern\IniHandleNeoxDashModel;
    use NeoxDashBoard\NeoxDashBoardBundle\Services\CrudHandleBuilder;
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

        public function __construct(readonly private CrudHandleBuilder $crudHandleBuilder)
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
            $neoxDashSection = new NeoxDashSection();
            $neoxDashSection->setClass($neoxDashClass);

            // Determine the template to use for rendering and render the builder !!
            $crudHandleBuilder = $this->setInit("edit", $neoxDashSection, [ 'id' => $neoxDashClass->getId() ]);

            /*
            * Call to the generic form management service, with support for turbo-stream
            * For kipping this code flexible to return your need
            */
            return $crudHandleBuilder
                ->handleCreateForm()
                ->handleForm($request)
                ->render()
            ;

        }

        #[Route('/{id}', name: 'app_neox_dash_section_show', methods: [ 'GET' ])]
        public function show(NeoxDashSection $neoxDashSection): Response
        {
            return $this->render('@NeoxDashBoardBundle/neox_dash_section/show.html.twig', [ 'neox_dash_section' => $neoxDashSection, ]);
        }


        #[Route('/{id}/edit', name: 'app_neox_dash_section_edit', methods: [ 'GET', 'POST' ])]
        public function edit(Request $request, NeoxDashSection $neoxDashSection): Response|JsonResponse
        {
           // Determine the template to use for rendering and render the builder !!
            $crudHandleBuilder = $this->setInit("edit", $neoxDashSection);

            /*
            * Call to the generic form management service, with support for turbo-stream
            * For kipping this code flexible to return your need
            */
            return $crudHandleBuilder
                ->handleCreateForm()
                ->handleForm($request)
                ->render()
            ;
        }

        #[Route('/exchange-{action}', name: 'app_neox_dash_section_exchange', defaults: [ 'action' => 'index'], methods: [
            'GET',
            'POST'
        ])]
        public function exchange(Request $request, string $action, NeoxDashSectionRepository $neoxDashSectionRepository, entityManagerInterface $entityManager): Response|JsonResponse
        {
            $content = $request->getContent();
            $data    = json_decode($content, true) ?? null;

            if ($action === "index") {
                // Recover both domains
                $draggedSection = $neoxDashSectionRepository->find($data[ "draggedId" ]);
                $targetSection  = $neoxDashSectionRepository->find($data[ "targetId" ]);
                $tempPosition = $targetSection->getPosition();
                $draggedSection->setPosition($tempPosition);
            }else{
                $draggedSection = $entityManager->getRepository(neoxDashDomain::class)->find($data[ "draggedId" ]);
                $targetSection  = $neoxDashSectionRepository->find($data[ "targetId" ]);
                $draggedSection->setSection($targetSection);
                $draggedSection->setPosition(0);
            }

            $entityManager->flush();

            return new JsonResponse("true");

        }

        #[Route('/{id}', name: 'app_neox_dash_section_delete', methods: [ 'POST' ])]
        public function delete(Request $request, NeoxDashSection $neoxDashSection, EntityManagerInterface $entityManager): Response
        {
            $submit = false;
            if ($this->isCsrfTokenValid('delete' . $neoxDashSection->getId(), $request->getPayload()->getString('_token'))) {
                $entityManager->remove($neoxDashSection);
                $entityManager->flush();
                $submit = true;
            }

            $crudHandleBuilder = $this->setInit("index");
            $return             = $this->crudHandleBuilder->getRequestType($request);

            return match ($return["status"]) {
                "redirect"  => $submit ? $this->redirectToRoute($crudHandleBuilder->getIniHandleNeoxDashModel()->getRoute() . 'index', [], Response::HTTP_SEE_OTHER) : null,
                "ajax"      => $submit ? new JsonResponse(true): new JsonResponse(false),
                "turbo"     => $submit ? $return[ "data" ] : false,
                default     => $this->render($crudHandleBuilder->getIniHandleNeoxDashModel()->getNew(), [ 'form' => $form->createView(), ]),
            };
        }

        /**
         * @return IniHandleNeoxDashModel
         */
        public function setInit(string $name = "new", object $object = null, array $params = []): CrudHandleBuilder
        {
            $o = $this->crudHandleBuilder->createNewHandleNeoxDashModel()
                    ->setNew("@NeoxDashBoardBundle/neox_dash_section/$name.html.twig")
                    ->setForm('@NeoxDashBoardBundle/neox_dash_section/_form.html.twig')
                    ->setRoute('app_neox_dash_section')
                    ->setParams($params)
                    ->setFormInterface(NeoxDashSectionType::class)
                    ->setEntity($object)
            ;

            // Determine the template to use for rendering
            return $this->crudHandleBuilder->setHandleNeoxDashModel($o);
        }
    }
