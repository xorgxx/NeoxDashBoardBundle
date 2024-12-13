<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Controller;

    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashClass;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashDomain;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashSection;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashSetup;
    use NeoxDashBoard\NeoxDashBoardBundle\Enum\NeoxDashTypeEnum;
    use NeoxDashBoard\NeoxDashBoardBundle\Enum\NeoxSizeEnum;
    use NeoxDashBoard\NeoxDashBoardBundle\Enum\NeoxStyleEnum;
    use NeoxDashBoard\NeoxDashBoardBundle\Pattern\IniHandleNeoxDashModel;
    use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashSectionRepository;
    use NeoxDashBoard\NeoxDashBoardBundle\Services\CrudHandleBuilder;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashWidget;
    use NeoxDashBoard\NeoxDashBoardBundle\Form\NeoxDashWidgetType;
    use Doctrine\ORM\EntityManagerInterface;
    use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashWidgetRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Attribute\Route;
    use Symfony\UX\Turbo\TurboBundle;

    #[Route('/neox/dash/widget')]
    final class NeoxDashWidgetController extends AbstractController
    {

        public function __construct(readonly private CrudHandleBuilder $crudHandleBuilder)
        {
        }

        #[Route('/', name: 'app_neox_dash_widget_index', methods: [ 'GET' ])]
        public function index(NeoxDashWidgetRepository $neoxDashWidgetRepository): Response
        {
            return $this->render('@NeoxDashBoardBundle/neox_dash_widget/index.html.twig', [ 'neox_dash_widgets' => $neoxDashWidgetRepository->findAll(), ]);
        }

        /*
         * =========== CRUD to add new widget ==========
         * To add new widget we need :
         *   - id of the setup
         *   - new the section
         *
         *   - new | exist. the class  <- here we can get the section
         *      - if we pass as parameter the id of the class this mean that we want to add a widget in this class
         *        if not mean that it's new class
         */
        #[Route('/new', name: 'app_neox_dash_widget_new', methods: ['GET', 'POST'])]
        public function new(Request $request, NeoxDashWidgetRepository $neoxDashWidgetRepository): Response | JsonResponse
        {
            /*
             * Find if widget exist and is not publish
             * if not exist we can create it !
             * if exist we can toggle publish !!
             */

            // build entity
            $neoxDashWidget     = new NeoxDashWidget();

            // Determine the template to use for rendering and render the builder !!
            $crudHandleBuilder  = $this->setInit("new", $neoxDashWidget);

            // PreHandle the form creation | meaning that we have check form from request it will return
            // true : form all is good
            // false : form is not good cant be persist §§§
            $return             = $crudHandleBuilder->handleCreateForm()->preHandleForm($request);

            // if submit is true we can now add any information before persist and flush !!
            if ($return->iniHandleNeoxDashModel->getReturn()[ "submit" ] ) {
                $formData = $request->request->all()["neox_dash_widget"];
                // look in class if it das exist ? if so then we just toggle publish and render
                if ( $neoxDashWidget = $neoxDashWidgetRepository->findOneByPublish( $formData["widget"] ) ) {
                    $neoxDashWidget->setPublish(true );
                    $neoxDashWidget->getSection()->getClass()->setPublish(true );
                    $crudHandleBuilder->entityManager->persist( $neoxDashWidget );
                    $crudHandleBuilder->entityManager->flush();
                }else{
                    $section        = new NeoxDashSection();
                    $section->setName( $formData["widget"] );
                    $section->setRow(6 );
                    $section->setPosition(1 );
                    $section->setsize(NeoxSizeEnum::COL12 );
                    $crudHandleBuilder->entityManager->persist( $section );

                    // if id null then it's a new class
                    $class          = new NeoxDashClass();
                    $class->addNeoxDashSection( $section );
                    $class->setNeoxDashSetup( $crudHandleBuilder->getNeoxDasSetup() );
                    $class->setName("Widget " . $formData["widget"] );
                    $class->settype(NeoxDashTypeEnum::TOOLS );
                    $class->setIcon("puzzle-piece" );
                    $class->setMode(NeoxStyleEnum::TABS );
                    $class->setPosition(1 );
                    $class->setsize(NeoxSizeEnum::COL6 );
                    $crudHandleBuilder->entityManager->persist( $class );

                    $crudHandleBuilder->iniHandleNeoxDashModel->getEntity()->setSection( $section );
                    $crudHandleBuilder->flushHandleForm();
                }

            }

            /*
            * Call to the generic form management service, with support for turbo-stream
            * For kipping this code flexible to return your need
            */
            return $crudHandleBuilder->render()
            ;

        }

        #[Route('/{id}', name: 'app_neox_dash_widget_show', methods: [ 'GET' ])]
        public function show(NeoxDashWidget $neoxDashWidget): Response
        {
            return $this->render('@NeoxDashBoardBundle/neox_dash_widget/show.html.twig', [ 'neox_dash_widget' => $neoxDashWidget, ]);
        }

        #[Route('/{id}/edit', name: 'app_neox_dash_widget_edit', methods: [
            'GET', 'POST'
        ])]
        public function edit(Request $request, NeoxDashWidget $neoxDashWidget): Response | JsonResponse
        {
            // Determine the template to use for rendering and render the builder !!
            $crudHandleBuilder = $this->setInit("edit", $neoxDashWidget);

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

        #[Route('/{id}', name: 'app_neox_dash_widget_delete', methods: [ 'POST' ])]
        public function delete(Request $request, NeoxDashWidget $neoxDashWidget, EntityManagerInterface $entityManager): Response
        {
            $submit = false;
            if ($this->isCsrfTokenValid('delete' . $neoxDashWidget->getId(), $request->getPayload()->getString('_token'))) {
                $entityManager->remove($neoxDashWidget);
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
            
//            if ($this->isCsrfTokenValid('delete' . $neoxDashWidget->getId(), $request->getPayload()->getString('_token'))) {
//                $entityManager->remove($neoxDashWidget);
//                $entityManager->flush();
//            }
//
//            return $this->redirectToRoute('app_neox_dash_widget_index', [], Response::HTTP_SEE_OTHER);
        }
        
        /**
         * @return IniHandleNeoxDashModel
         */
        public function setInit(string $name = "new", object $object = null, array $params = []): CrudHandleBuilder
        {
            $o = $this->crudHandleBuilder
                ->createNewHandleNeoxDashModel()
                ->setNew("@NeoxDashBoardBundle/neox_dash_widget/$name.html.twig")
                ->setForm('@NeoxDashBoardBundle/neox_dash_widget/_form.html.twig')
                ->setRoute('app_neox_dash_widget')
                ->setParams($params)
                ->setFormInterface(NeoxDashWidgetType::class)
                ->setEntity($object)
            ;

            // Determine the template to use for rendering
            return $this->crudHandleBuilder->setHandleNeoxDashModel($o);
        }
    }
