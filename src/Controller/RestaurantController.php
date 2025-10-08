<?php

namespace App\Controller;

use App\Entity\CollaborateurRestaurantFiltre;
use App\Entity\Restaurant;
use App\Entity\RestaurantFiltre;
use App\Form\CollaborateurRestaurantFiltreType;
use App\Form\RestaurantFiltreType;
use App\Form\RestaurantType;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/restaurant')]
final class RestaurantController extends AbstractController
{
    #[Route(name: 'app_restaurant_index', methods: ['GET'])]
    public function index(
        RestaurantRepository $restaurantRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        $restaurantFiltre = new RestaurantFiltre();
        $form = $this->createForm(RestaurantFiltreType::class, $restaurantFiltre);
        $form->handleRequest($request);

        $query = $restaurantRepository->findAllWithFilter($restaurantFiltre);

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $pagination,
            'form' => $form,
        ]);
    }

    #[Route('/new', name: 'app_restaurant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $restaurant = new Restaurant();
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($restaurant);
            $entityManager->flush();

            $this->addFlash('success', 'Le restaurant a bien été créé.');

            return $this->redirectToRoute('app_restaurant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('restaurant/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_restaurant_show', methods: ['GET'])]
    public function show(
        Restaurant $restaurant,
        RestaurantRepository $repository,
        Request $request
    ): Response {
        $form = $this->createForm(RestaurantType::class, $restaurant);

        $collaborateurRestaurantFiltre = new CollaborateurRestaurantFiltre();
        $collaborateurRestaurantForm = $this->createForm(
            CollaborateurRestaurantFiltreType::class,
            $collaborateurRestaurantFiltre
        );
        $collaborateurRestaurantForm->handleRequest($request);

        $affectations = $repository->findCurrentAffectationsWithFilter(
            $restaurant->getId(),
            $collaborateurRestaurantFiltre
        );

        return $this->render('restaurant/show.html.twig', [
            'restaurant' => $restaurant,
            'affectations' => $affectations,
            'form' => $form,
            'collaborateurRestaurantForm' => $collaborateurRestaurantForm,
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_restaurant_edit', methods: ['POST'])]
    public function editRestaurant(
        Request $request,
        Restaurant $restaurant,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le restaurant a bien été modifié.');

            return $this->redirectToRoute('app_restaurant_show', ['id' => $restaurant->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('app_restaurant_show', ['id' => $restaurant->getId()]);
    }

    #[Route('/{id}/confirmer-suppression', name: 'app_restaurant_delete_confirm', methods: ['GET'])]
    public function confirmDelete(Restaurant $restaurant): Response
    {
        return $this->render('restaurant/_delete_modal.html.twig', [
            'restaurant' => $restaurant,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'app_restaurant_delete', methods: ['POST'])]
    public function delete(Request $request, Restaurant $restaurant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$restaurant->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($restaurant);
            $entityManager->flush();

            $this->addFlash('success', 'Le restaurant a bien été supprimé.');
        }

        return $this->redirectToRoute('app_restaurant_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/affectations/{id}', name: 'app_restaurant_affectations', methods: ['GET'])]
    public function affectationsRestaurant(
        Restaurant $restaurant,
        Request $request,
        RestaurantRepository $repository
    ): Response {
        $collaborateurRestaurantFiltre = new CollaborateurRestaurantFiltre();
        $form = $this->createForm(
            CollaborateurRestaurantFiltreType::class,
            $collaborateurRestaurantFiltre
        );
        $form->handleRequest($request);

        $affectations = $repository->findAllAffectationsWithFilter(
            $restaurant->getId(),
            $collaborateurRestaurantFiltre
        );

        return $this->render('restaurant/affectations.html.twig', [
            'restaurant' => $restaurant,
            'affectations' => $affectations,
            'form' => $form,
        ]);
    }
}
