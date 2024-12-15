<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Controller;

    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashDomain;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashDomainHistory;
    use NeoxDashBoard\NeoxDashBoardBundle\Form\NeoxDashDomainHistoryType;
    use NeoxDashBoard\NeoxDashBoardBundle\Services\CrudHandleBuilder;
    use Doctrine\ORM\EntityManagerInterface;
    use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashDomainHistoryRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Attribute\Route;

    #[Route('/neox/dash/domain-history')]
    final class NeoxDashDomainHistoryController extends AbstractController
    {

        public function __construct(readonly private CrudHandleBuilder $crudHandleBuilder) {}

        #[Route('/', name: 'app_neox_dash_domain_history_index', methods: [ 'GET' ])]
        public function index(NeoxDashDomainHistoryRepository $neoxDashDomainHistoryRepository): Response
        {
            return $this->render('@NeoxDashBoardBundle/neox_dash_domain_history/index.html.twig', [ 'neox_dash_setups' => $neoxDashDomainHistoryRepository->findAll(), ]);
        }

        #[Route('/new', name: 'app_neox_dash_domain_history_new', methods: [
            'GET',
            'POST'
        ])]
        public function new(Request $request): Response|JsonResponse
        {

            $neoxDashDomainHistory = new NeoxDashDomainHistory();

            // Determine the template to use for rendering and render the builder !!
            $crudHandleBuilder = $this->setInit("new", $neoxDashDomainHistory);

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

        #[Route('/{id}', name: 'app_neox_dash_domain_history_show', methods: [ 'GET' ])]
        public function show(NeoxDashDomainHistory $neoxDashDomainHistory): Response
        {
            return $this->render('@NeoxDashBoardBundle/index.html.twig', [ 'NeoxDashDomainHistory' => $neoxDashDomainHistory, ]);
        }

        #[Route('/{id}/edit', name: 'app_neox_dash_domain_history_edit', methods: [
            'GET',
            'POST'
        ])]
        public function edit(Request $request, NeoxDashDomainHistory $neoxDashDomainHistory): Response|JsonResponse
        {

            // Determine the template to use for rendering and render the builder !!
            $crudHandleBuilder = $this->setInit("edit", $neoxDashDomainHistory);

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

        #[Route('/{id}', name: 'app_neox_dash_domain_history_delete', methods: [ 'POST' ])]
        public function delete(Request $request, NeoxDashDomainHistory $neoxDashDomainHistory, EntityManagerInterface $entityManager): Response
        {
            if ($this->isCsrfTokenValid('delete' . $neoxDashDomainHistory->getId(), $request
                ->getPayload()
                ->getString('_token'))) {
                $entityManager->remove($neoxDashDomainHistory);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_neox_dash_domain_history_index', [], Response::HTTP_SEE_OTHER);
        }

//        #[Route('/count/{id}', name: 'app_neox_dash_domain_history_count', methods: [ 'POST' ])]
//        public function count(Request $request, NeoxDashDomain $neoxDashDomain, EntityManagerInterface $entityManager): Response
//        {
//            $count = 0;
//            $data  = json_decode(file_get_contents('php://input'), true);
//
//            if ($this->isCsrfTokenValid('count' . $neoxDashDomain->getId(), $request
//                ->getPayload()
//                ->getString('_token'))) {
//                if ($DomainHistory = $neoxDashDomain->getNeoxDashDomainsHistory()[ 0 ]) {
//                    $count = $DomainHistory->getCount();
//                }
//                else {
//                    $DomainHistory = new NeoxDashDomainHistory();
//                    $DomainHistory->setDomain($neoxDashDomain);
//                }
//                $count++;
//                $DomainHistory->setCount($count);
//                $entityManager->persist($DomainHistory);
//                $entityManager->flush();
//            }
//
//            return new JsonResponse("true");
//        }


        /**
         * @param string $name
         * @param object|null $object
         * @param array $params
         *
         * @return CrudHandleBuilder
         */
        public function setInit(string $name = "new", object $object = null, array $params = []): CrudHandleBuilder
        {
            $o = $this->crudHandleBuilder
                ->createNewHandleNeoxDashModel()
                ->setNew("@NeoxDashBoardBundle/neox_dash_domain_history/$name.html.twig")
                ->setForm('@NeoxDashBoardBundle/neox_dash_domain_history/_form.html.twig')
                ->setRoute('app_neox_dash_domain_history')
                ->setParams($params)
                ->setFormInterface(NeoxDashDomainHistoryType::class)
                ->setEntity($object)
            ;

            // Determine the template to use for rendering
            return $this->crudHandleBuilder->setHandleNeoxDashModel($o);
        }
    }
