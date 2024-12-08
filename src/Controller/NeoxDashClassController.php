<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Controller;

    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashDomain;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashSection;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashSetup;
    use NeoxDashBoard\NeoxDashBoardBundle\Pattern\IniHandleNeoxDashModel;
    use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashSectionRepository;
    use NeoxDashBoard\NeoxDashBoardBundle\Services\CrudHandleBuilder;
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

        public function __construct(readonly private CrudHandleBuilder $crudHandleBuilder)
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

            // build entity
            $neoxDashClass      = new NeoxDashClass();
            // Determine the template to use for rendering and render the builder !!
            $crudHandleBuilder  = $this->setInit("new", $neoxDashClass);

            $section            = new NeoxDashSection();
            $section->setName("New (edit to modify");
            $section->setHeight(2.5);

            $crudHandleBuilder->iniHandleNeoxDashModel->getEntity()->addNeoxDashSection($section);
            $crudHandleBuilder->iniHandleNeoxDashModel->getEntity()->setNeoxDashSetup($crudHandleBuilder->getNeoxDasSetup());

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
            // Determine the template to use for rendering and render the builder !!
            $crudHandleBuilder = $this->setInit("edit", $neoxDashClass);

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

        #[Route('/{id}', name: 'app_neox_dash_class_delete', methods: [ 'POST' ])]
        public function delete(Request $request, NeoxDashClass $neoxDashClass, EntityManagerInterface $entityManager): Response
        {
            $submit = false;
            if ($this->isCsrfTokenValid('delete' . $neoxDashClass->getId(), $request->getPayload()->getString('_token'))) {
                $entityManager->remove($neoxDashClass);
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
            
//            if ($this->isCsrfTokenValid('delete' . $neoxDashClass->getId(), $request->getPayload()->getString('_token'))) {
//                $entityManager->remove($neoxDashClass);
//                $entityManager->flush();
//            }
//
//            return $this->redirectToRoute('app_neox_dash_class_index', [], Response::HTTP_SEE_OTHER);
        }
        
        /**
         * @return IniHandleNeoxDashModel
         */
        public function setInit(string $name = "new", object $object = null, array $params = []): CrudHandleBuilder
        {
            $o = $this->crudHandleBuilder
                ->createNewHandleNeoxDashModel()
                ->setNew("@NeoxDashBoardBundle/neox_dash_class/$name.html.twig")
                ->setForm('@NeoxDashBoardBundle/neox_dash_class/_form.html.twig')
                ->setRoute('app_neox_dash_class')
                ->setParams($params)
                ->setFormInterface(NeoxDashClassType::class)
                ->setEntity($object)
            ;

            // Determine the template to use for rendering
            return $this->crudHandleBuilder->setHandleNeoxDashModel($o);
        }
    }
