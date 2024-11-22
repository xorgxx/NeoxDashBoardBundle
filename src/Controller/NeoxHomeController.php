<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Controller;

    use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashSetupRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Attribute\Route;

    class NeoxHomeController extends AbstractController
    {
        #[Route('/neox/dash/neox-home', name: 'app_neox_dashboard_home')]
        public function dashBoard(NeoxDashSetupRepository $setupRepository): Response
        {

            $NeoxDashSetup = $setupRepository->findOneSetup(["id"=>1]);
            return $this->render('@NeoxDashBoardBundle/indexHome.html.twig', [
                'NeoxDashSetup' => $NeoxDashSetup,
            ]);
        }
    }
