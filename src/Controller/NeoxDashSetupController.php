<?php

namespace NeoxDashBoard\NeoxDashBoardBundle\Controller;

use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashSetup;
use NeoxDashBoard\NeoxDashBoardBundle\Form\NeoxDashSetupType;
use Doctrine\ORM\EntityManagerInterface;
use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashSetupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/neox/dash/setup')]
final class NeoxDashSetupController extends AbstractController
{
    #[Route('/',name: 'app_neox_dash_setup_index', methods: ['GET'])]
    public function index(NeoxDashSetupRepository $neoxDashSetupRepository): Response
    {
        return $this->render('@NeoxDashBoardBundle/neox_dash_setup/index.html.twig', [
            'neox_dash_setups' => $neoxDashSetupRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_neox_dash_setup_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $neoxDashSetup = new NeoxDashSetup();
        $form = $this->createForm(NeoxDashSetupType::class, $neoxDashSetup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($neoxDashSetup);
            $entityManager->flush();

            return $this->redirectToRoute('app_neox_dash_setup_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('@NeoxDashBoardBundle/@NeoxDashBoardBundle/neox_dash_setup/new.html.twig', [
            'neox_dash_setup' => $neoxDashSetup,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_neox_dash_setup_show', methods: ['GET'])]
    public function show(NeoxDashSetup $neoxDashSetup): Response
    {
        return $this->render('@NeoxDashBoardBundle/neox_dash_setup/show.html.twig', [
            'neox_dash_setup' => $neoxDashSetup,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_neox_dash_setup_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, NeoxDashSetup $neoxDashSetup, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NeoxDashSetupType::class, $neoxDashSetup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_neox_dash_setup_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('@NeoxDashBoardBundle/neox_dash_setup/edit.html.twig', [
            'neox_dash_setup' => $neoxDashSetup,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_neox_dash_setup_delete', methods: ['POST'])]
    public function delete(Request $request, NeoxDashSetup $neoxDashSetup, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$neoxDashSetup->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($neoxDashSetup);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_neox_dash_setup_index', [], Response::HTTP_SEE_OTHER);
    }
}
