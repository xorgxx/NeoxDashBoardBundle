<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Controller;

    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashSection;
    use NeoxDashBoard\NeoxDashBoardBundle\Pattern\IniHandleNeoxDashModel;
    use NeoxDashBoard\NeoxDashBoardBundle\Services\FormHandlerService;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashDomain;
    use NeoxDashBoard\NeoxDashBoardBundle\Form\NeoxDashDomainType;
    use Doctrine\ORM\EntityManagerInterface;
    use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashDomainRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Attribute\Route;
    use Symfony\UX\Turbo\TurboBundle;

    #[Route('/neox/dash/domain')]
    final class NeoxDashDomainController extends AbstractController
    {

        public function __construct(readonly private FormHandlerService $formHandlerService)
        {
        }

        #[Route('/', name: 'app_neox_dash_domain_index', methods: [ 'GET' ])]
        public function index(NeoxDashDomainRepository $neoxDashDomainRepository): Response
        {
            return $this->render('@NeoxDashBoardBundle/neox_dash_domain/index.html.twig', [ 'neox_dash_domains' => $neoxDashDomainRepository->findAll(), ]);
        }

        #[Route('/new/{id}', name: 'app_neox_dash_domain_new', methods: [ 'GET', 'POST' ])]
        public function new(Request $request, NeoxDashSection $neoxDashSection): Response | JsonResponse
        {
//            $formHandlerService = $this->setInit("new", [ "id" => $neoxDashSection->getId() ]);

            // build entity
            $neoxDashDomain = new NeoxDashDomain();
            $neoxDashDomain->setSection($neoxDashSection);

            $r = rand(0, 255); // Rouge
            $g = rand(0, 255); // Vert
            $b = rand(0, 255); // Bleu
            $neoxDashDomain->setColor(sprintf("#%02x%02x%02x", $r, $g, $b));

            // Determine the template to use for rendering and render the builder !!
            $formHandlerService = $this->setInit("new", $neoxDashDomain, [ "id" => $neoxDashSection->getId() ]);

            /*
            * Call to the generic form management service, with support for turbo-stream
            * For kipping this code flexible to return your need
            */
            return $formHandlerService
                ->handleCreateForm()
                ->handleForm($request)
                ->renderNeox()
            ;

        }

        #[Route('/{id}', name: 'app_neox_dash_domain_show', methods: [ 'GET' ])]
        public function show(NeoxDashDomain $neoxDashDomain): Response
        {
            return $this->render('@NeoxDashBoardBundle/neox_dash_domain/show.html.twig', [ 'neox_dash_domain' => $neoxDashDomain, ]);
        }

        #[Route('/{id}/edit', name: 'app_neox_dash_domain_edit', methods: [
            'GET', 'POST'
        ])]
        public function edit(Request $request, NeoxDashDomain $neoxDashDomain): Response | JsonResponse
        {
            // Determine the template to use for rendering and render the builder !!
            $formHandlerService = $this->setInit("edit", $neoxDashDomain);

            /*
            * Call to the generic form management service, with support for turbo-stream
            * For kipping this code flexible to return your need
            */
            return $formHandlerService
                ->handleCreateForm()
                ->handleForm($request)
                ->renderNeox()
            ;

        }

        #[Route('/{id}', name: 'app_neox_dash_domain_delete', methods: [ 'POST' ])]
        public function delete(Request $request, NeoxDashDomain $neoxDashDomain, EntityManagerInterface $entityManager): Response
        {
            $submit = false;
            if ($this->isCsrfTokenValid('delete' . $neoxDashDomain->getId(), $request->getPayload()->getString('_token'))) {
                $entityManager->remove($neoxDashDomain);
                $entityManager->flush();
                $submit = true;
            }

            $formHandlerService = $this->setInit("index");
            $return             = $this->formHandlerService->getRequestType($request);

            return match ($return["status"]) {
                "redirect"  => $submit ? $this->redirectToRoute($formHandlerService->getIniHandleNeoxDashModel()->getRoute() . 'index', [], Response::HTTP_SEE_OTHER) : null,
                "ajax"      => $submit ? new JsonResponse(true): new JsonResponse(false),
                "turbo"     => $submit ? $return[ "data" ] : false,
                default     => $this->render($formHandlerService->getIniHandleNeoxDashModel()->getNew(), [ 'form' => $form->createView(), ]),
            };
//            return $this->redirectToRoute('app_neox_dash_domain_index', [], Response::HTTP_SEE_OTHER);
        }

        /**
         * @return IniHandleNeoxDashModel
         */
        public function setInit(string $name = "new", object $object = null, array $params = []): FormHandlerService
        {
            $o = $this->formHandlerService
                ->createNewHandleNeoxDashModel()
                ->setNew("@NeoxDashBoardBundle/neox_dash_domain/$name.html.twig")
                ->setForm('@NeoxDashBoardBundle/neox_dash_domain/_form.html.twig')
                ->setRoute('app_neox_dash_domain')
                ->setParams($params)
                ->setFormInterface(NeoxDashDomainType::class)
                ->setEntity($object)
            ;

            // Determine the template to use for rendering
            return $this->formHandlerService->setHandleNeoxDashModel($o);
        }
    }
