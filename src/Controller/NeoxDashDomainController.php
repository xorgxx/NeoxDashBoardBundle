<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Controller;

    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashSection;
    use NeoxDashBoard\NeoxDashBoardBundle\Pattern\IniHandleNeoxDashModel;
    use NeoxDashBoard\NeoxDashBoardBundle\Services\CrudHandleBuilder;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashDomain;
    use NeoxDashBoard\NeoxDashBoardBundle\Form\NeoxDashDomainType;
    use Doctrine\ORM\EntityManagerInterface;
    use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashDomainRepository;
    use NeoxDashBoard\NeoxDashBoardBundle\Services\FindIconOnWebSite;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Attribute\Route;
    use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
    use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
    use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
    use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
    use Symfony\Contracts\HttpClient\HttpClientInterface;
    use Symfony\UX\Turbo\TurboBundle;

    #[Route('/neox/dash/domain')]
    final class NeoxDashDomainController extends AbstractController
    {

        public function __construct(readonly private CrudHandleBuilder $crudHandleBuilder, readonly FindIconOnWebSite $findIconOnWebSite)
        {
        }

        #[Route('/', name: 'app_neox_dash_domain_index', methods: [ 'GET' ])]
        public function index(NeoxDashDomainRepository $neoxDashDomainRepository): Response
        {
            $domains = $neoxDashDomainRepository->findBy([], [ 'Position' => 'ASC' ]);
            return $this->render('@NeoxDashBoardBundle/neox_dash_domain/index.html.twig', [ 'neox_dash_domains' => $domains ]);
        }

        #[Route('/new/{id}', name: 'app_neox_dash_domain_new', methods: [
            'GET',
            'POST'
        ])]
        public function new(Request $request, NeoxDashSection $neoxDashSection): Response|JsonResponse
        {
//            $crudHandleBuilder = $this->setInit("new", [ "id" => $neoxDashSection->getId() ]);
            $content = $request->getContent();
            $data    = json_decode($content, true) ?? null;
            
            
            // build entity
            $neoxDashDomain = new NeoxDashDomain();
            $neoxDashDomain->setSection($neoxDashSection);
            if ($data[ "domain" ] ?? null) {
                $d = $this->findIconOnWebSite->extractDomain($data[ "domain" ]);
                $neoxDashDomain->setName($d[ "domain" ]);
                $neoxDashDomain->setUrl($data[ "domain" ] ?? "");
            }
            $neoxDashDomain->setUrlIcon("z");

            $r = random_int(0, 255); // Rouge
            $g = random_int(0, 255); // Vert
            $b = random_int(0, 255); // Bleu

            $neoxDashDomain->setColor(sprintf("#%02x%02x%02x", $r, $g, $b));

            // Determine the template to use for rendering and render the builder !!
            $crudHandleBuilder = $this->setInit("new", $neoxDashDomain, [ "id" => $neoxDashSection->getId() ]);

            /*
            * Call to the generic form management service, with support for turbo-stream
            * For kipping this code flexible to return your need
            */

            /*
            *   ===== this is the way to use the generic form management service =====
            *   $handleSubmit = $crudHandleBuilder->handleCreateForm()->preHandleForm($request);
            *   // Handle form submission for any entity, can make entity change if needed and flush entity
            *   $handleSubmit->getIniHandleNeoxDashModel()->getEntity()
            *       ->setUrlIcon($handleSubmit->getIniHandleNeoxDashModel()->getEntity()->getUrlIcon())
            *   ;
            *
            *   return $handleSubmit->flushHandleForm()->render();
            */

            $handleSubmit = $crudHandleBuilder->handleCreateForm()->preHandleForm($request);
            $url = $handleSubmit->getIniHandleNeoxDashModel()->getEntity()->geturl();
            // check if it exist in dBase
            if ($url) {
                $hash   = hash('sha256', $url);
                $o      = $crudHandleBuilder->entityManager->getRepository(neoxDashDomain::class)->findOneBy([ "hash" => $hash ]);
                if ( $o ) {
                    return new jsonResponse("exist");
                }
            }

            return $handleSubmit->flushHandleForm()->render();

//            return $crudHandleBuilder
//                ->handleCreateForm()
//                ->handleForm($request)
//                ->render()
//            ;

        }

        #[Route('/{id}', name: 'app_neox_dash_domain_show', methods: [ 'GET' ])]
        public function show(NeoxDashDomain $neoxDashDomain): Response
        {
            return $this->render('@NeoxDashBoardBundle/neox_dash_domain/show.html.twig', [ 'neox_dash_domain' => $neoxDashDomain, ]);
        }

        /**
         * @throws TransportExceptionInterface
         */
        #[Route('/{id}/edit', name: 'app_neox_dash_domain_edit', methods: [
            'GET',
            'POST'
        ])]
        public function edit(Request $request, NeoxDashDomain $neoxDashDomain): Response|JsonResponse
        {
            // Determine the template to use for rendering and render the builder !!
            $crudHandleBuilder = $this->setInit("edit", $neoxDashDomain);

//            $icon = $this->findIconOnWebSite->getFaviconUrl($neoxDashDomain->getUrl());
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

        #[Route('/exchange-{action}', defaults: ['action' => 'index'], name: 'app_neox_dash_domain_exchange', methods: [
            'GET',
            'POST'
        ])]
        public function exchange(Request $request, string $action, NeoxDashDomainRepository $neoxDashDomainRepository, entityManagerInterface $entityManager): Response|JsonResponse
        {
            $content = $request->getContent();
            $data    = json_decode($content, true) ?? null;

            // Recover both domains
            $draggedDomain = $neoxDashDomainRepository->find($data[ "draggedId" ]);
            $targetDomain  = $neoxDashDomainRepository->find($data[ "targetId" ]);

            if ($draggedDomain && $targetDomain) {
                $tempPosition = $targetDomain->getPosition();
                $draggedDomain->setPosition($tempPosition);

                // Save changes
                $entityManager->flush();
                
                return new JsonResponse("true");
            }

            return new jsonResponse($targetId
                    ->getSection()
                    ->getId() === $draggedId
                    ->getSection()
                    ->getId());
        }

        #[Route('/{id}', name: 'app_neox_dash_domain_delete', methods: [ 'POST' ])]
        public function delete(Request $request, NeoxDashDomain $neoxDashDomain, EntityManagerInterface $entityManager, CsrfTokenManagerInterface $csrfTokenManager): Response
        {

            $csrfToken = $csrfTokenManager->getToken('delete' . $neoxDashDomain->getId())->getValue();
            $submittedToken = $request->get('_token');
            $id = "delete".(string)$neoxDashDomain->getId();

            $submit = $this->isCsrfTokenValid($id, $csrfToken);

            if ($submit)
            {
                $entityManager->remove($neoxDashDomain);
                $entityManager->flush();
            }

            $crudHandleBuilder = $this->setInit("index");
            $return            = $this->crudHandleBuilder->getRequestType($request);

            return match ($return[ "status" ]) {
                "redirect" => $submit ? $this->redirectToRoute($crudHandleBuilder
                        ->getIniHandleNeoxDashModel()
                        ->getRoute() . 'index', [], Response::HTTP_SEE_OTHER) : null,
                "ajax" => $submit ? new JsonResponse(true) : new JsonResponse(false),
                "turbo" => $submit ? $return[ "data" ] : false,
                default => $this->render($crudHandleBuilder
                    ->getIniHandleNeoxDashModel()
                    ->getNew(), [ 'form' => $form->createView(), ]),
            };
//            return $this->redirectToRoute('app_neox_dash_domain_index', [], Response::HTTP_SEE_OTHER);
        }

        /**
         * @return IniHandleNeoxDashModel
         */
        public function setInit(string $name = "new", object $object = null, array $params = []): CrudHandleBuilder
        {
            $o = $this->crudHandleBuilder
                ->createNewHandleNeoxDashModel()
                ->setNew("@NeoxDashBoardBundle/neox_dash_domain/$name.html.twig")
                ->setForm('@NeoxDashBoardBundle/neox_dash_domain/_form.html.twig')
                ->setRoute('app_neox_dash_domain')
                ->setParams($params)
                ->setFormInterface(NeoxDashDomainType::class)
                ->setEntity($object)
            ;

            // Determine the template to use for rendering
            return $this->crudHandleBuilder->setHandleNeoxDashModel($o);
        }

        #[Route('/{id}/find-icon', name: 'app_neox_dash_find-icon', methods: [ 'POST' ])]
        public function findIcon(Request $request, NeoxDashSection $neoxDashSection, EntityManagerInterface $entityManager): Response|JsonResponse
        {
            $domains = $neoxDashSection->getNeoxDashDomains();
            foreach ($domains as $domain) {
                $domain->seturlIcon($this->findIconOnWebSite->getFaviconUrl($domain->getname()));
                $entityManager->persist($domain);
            }
            $entityManager->flush();
            $submit            = true;
            $crudHandleBuilder = $this->setInit("index");
            $return            = $this->crudHandleBuilder->getRequestType($request);

            return match ($return[ "status" ]) {
                "redirect" => $submit ? $this->redirectToRoute($crudHandleBuilder
                        ->getIniHandleNeoxDashModel()
                        ->getRoute() . 'index', [], Response::HTTP_SEE_OTHER) : null,
                "ajax" => $submit ? new JsonResponse(true) : new JsonResponse(false),
                "turbo" => $submit ? $return[ "data" ] : false,
                default => null,
            };
        }

//        private function extractDomain($url) {
//
//            if (!preg_match('/^(https?|ftp):\/\//', $url)) {
//                $url = 'http://' . $url; // Ajoute un schéma par défaut
//            }
//            $parsedUrl = parse_url($url);
//
//            // Vérifier si le domaine existe et retourner le domaine sans www
//            $domain = isset($parsedUrl['host']) ? preg_replace('/^www\./', '', $parsedUrl['host']) : $_SERVER['HTTP_HOST'];
//            return  [
//                "domain"    => $domain,
//                "host"      => $parsedUrl['host'],
//            ];
//
//        }
    }
