<?php

namespace App\Controller;

use App\Entity\Affectation;
use App\Entity\Collaborateur;
use App\Entity\CollaborateurFiltre;
use App\Form\AffectationToCollaborateurType;
use App\Form\CollaborateurFiltreType;
use App\Form\CollaborateurType;
use App\Repository\CollaborateurRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/collaborateur')]
final class CollaborateurController extends AbstractController
{
    #[Route(name: 'app_collaborateur_index', methods: ['GET'])]
    public function index(CollaborateurRepository $collaborateurRepository, Request $request): Response
    {
        $filtre = new CollaborateurFiltre();
        $form = $this->createForm(CollaborateurFiltreType::class, $filtre);
        $form->handleRequest($request);

        return $this->render('collaborateur/index.html.twig', [
            'collaborateurs' => $collaborateurRepository->findAllWithFilter($filtre),
            'form' => $form,
        ]);
    }

    #[Route('/new', name: 'app_collaborateur_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UtilisateurRepository $utilisateurRepository
    ): Response {
        $collaborateur = new Collaborateur();
        $form = $this->createForm(CollaborateurType::class, $collaborateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($collaborateur);
            $entityManager->flush();

            if ($collaborateur->isAdministrateur()) {
                $utilisateur = $utilisateurRepository->findOneByEmail($collaborateur->getEmail());
                if (null !== $utilisateur) {
                    $utilisateur->setRoles(['ROLE_ADMIN']);
                    $entityManager->flush();
                }
            }

            return $this->redirectToRoute('app_collaborateur_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->render('collaborateur/new.html.twig', [
            'collaborateur' => $collaborateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_collaborateur_show', methods: ['GET'])]
    public function show(Collaborateur $collaborateur): Response
    {
        return $this->render('collaborateur/show.html.twig', [
            'collaborateur' => $collaborateur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_collaborateur_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Collaborateur $collaborateur,
        EntityManagerInterface $entityManager,
        UtilisateurRepository $utilisateurRepository
    ): Response {
        $form = $this->createForm(CollaborateurType::class, $collaborateur);

        $affectation = new Affectation();
        $affectationForm = $this->createForm(AffectationToCollaborateurType::class, $affectation);

        $form->handleRequest($request);
        $affectationForm->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $utilisateur = $utilisateurRepository->findOneByEmail($collaborateur->getEmail());

            if (null !== $utilisateur) {
                $utilisateur->setRoles($collaborateur->isAdministrateur() ? ['ROLE_ADMIN'] : []);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_collaborateur_show', ['id' => $collaborateur->getId()], Response::HTTP_SEE_OTHER);
        }

        if ($affectationForm->isSubmitted() && $affectationForm->isValid()) {
            $affectation->setCollaborateur($collaborateur);
            $entityManager->persist($affectation);
            $entityManager->flush();

            return $this->redirectToRoute('app_collaborateur_show', ['id' => $collaborateur->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('collaborateur/edit.html.twig', [
            'collaborateur' => $collaborateur,
            'form' => $form,
            'affectationForm' => $affectationForm,
        ]);
    }

    #[Route('/{id}', name: 'app_collaborateur_delete', methods: ['POST'])]
    public function delete(Request $request, Collaborateur $collaborateur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$collaborateur->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($collaborateur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_collaborateur_index', [], Response::HTTP_SEE_OTHER);
    }
}
