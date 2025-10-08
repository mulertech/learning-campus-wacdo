<?php

namespace App\Controller;

use App\Entity\Fonction;
use App\Form\FonctionType;
use App\Repository\FonctionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/fonction')]
final class FonctionController extends AbstractController
{
    #[Route(name: 'app_fonction_index', methods: ['GET'])]
    public function index(FonctionRepository $fonctionRepository): Response
    {
        return $this->render('fonction/index.html.twig', [
            'fonctions' => $fonctionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_fonction_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $fonction = new Fonction();
        $form = $this->createForm(FonctionType::class, $fonction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($fonction);
            $entityManager->flush();

            $this->addFlash('success', 'La fonction a bien été créée.');

            return $this->redirectToRoute('app_fonction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fonction/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_fonction_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Fonction $fonction, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FonctionType::class, $fonction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La fonction a bien été modifiée.');

            return $this->redirectToRoute('app_fonction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fonction/edit.html.twig', [
            'fonction' => $fonction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/confirmer-suppression', name: 'app_fonction_delete_confirm', methods: ['GET'])]
    public function confirmDelete(Fonction $fonction): Response
    {
        return $this->render('fonction/_delete_modal.html.twig', [
            'fonction' => $fonction,
        ]);
    }

    #[Route('/{id}', name: 'app_fonction_delete', methods: ['POST'])]
    public function delete(Request $request, Fonction $fonction, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fonction->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($fonction);
            $entityManager->flush();

            $this->addFlash('success', 'La fonction a bien été supprimée.');
        }

        return $this->redirectToRoute('app_fonction_index', [], Response::HTTP_SEE_OTHER);
    }
}
