<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Controller;

    use NeoxDashBoard\NeoxDashBoardBundle\Pattern\IniHandleNeoxDashModel;
    use NeoxDashBoard\NeoxDashBoardBundle\Services\CrudHandleBuilder;
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

        public function __construct(readonly private CrudHandleBuilder $crudHandleBuilder)
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

            $neoxDashSetup      = new NeoxDashSetup();

            // Determine the template to use for rendering and render the builder !!
            $crudHandleBuilder = $this->setInit("new", $neoxDashSetup);

            /*
            * Call to the generic form management service, with support for turbo-stream
            * For kipping this code flexible to return your need
            */
            return $crudHandleBuilder
                ->handleCreateForm($neoxDashSetup, NeoxDashSetupType::class)
                ->handleForm($request, null, $neoxDashSetup)
                ->render()
            ;

        }

        #[Route('/{id}', name: 'app_neox_dash_setup_show', methods: [ 'GET' ])]
        public function show(NeoxDashSetup $neoxDashSetup): Response
        {
            return $this->render('@NeoxDashBoardBundle/index.html.twig', [ 'NeoxDashSetup' => $neoxDashSetup, ]);
        }

        #[Route('/{id}/edit', name: 'app_neox_dash_setup_edit', methods: [
            'GET', 'POST'
        ])]
        public function edit(Request $request, NeoxDashSetup $neoxDashSetup): Response | JsonResponse
        {
            
            // Determine the template to use for rendering and render the builder !!
            $crudHandleBuilder = $this->setInit("edit", $neoxDashSetup);

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

        #[Route('/{id}', name: 'app_neox_dash_setup_delete', methods: [ 'POST' ])]
        public function delete(Request $request, NeoxDashSetup $neoxDashSetup, EntityManagerInterface $entityManager): Response
        {
            if ($this->isCsrfTokenValid('delete' . $neoxDashSetup->getId(), $request->getPayload()->getString('_token'))) {
                $entityManager->remove($neoxDashSetup);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_neox_dash_setup_index', [], Response::HTTP_SEE_OTHER);
        }

        /**
         * @return IniHandleNeoxDashModel
         */
        public function setInit(string $name = "new", object $object = null, array $params = []): CrudHandleBuilder
        {
            $o = $this->crudHandleBuilder
                ->createNewHandleNeoxDashModel()
                ->setNew("@NeoxDashBoardBundle/neox_dash_setup/$name.html.twig")
                ->setForm('@NeoxDashBoardBundle/neox_dash_setup/_form.html.twig')
                ->setRoute('app_neox_dash_setup')
                ->setParams($params)
                ->setFormInterface(NeoxDashSetupType::class)
                ->setEntity($object)
            ;

            // Determine the template to use for rendering
            return $this->crudHandleBuilder->setHandleNeoxDashModel($o);
        }
    }
