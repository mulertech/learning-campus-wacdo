<?php

namespace App\Twig\Components;

use App\Entity\CollaborateurRestaurantFiltre;
use App\Entity\Fonction;
use App\Form\CollaborateurRestaurantFiltreType;
use App\Repository\RestaurantRepository;
use DateTime;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('restaurant_show')]
class RestaurantShow extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp(writable: true, onUpdated: 'onPropUpdate')]
    public ?Fonction $fonction = null;

    #[LiveProp(writable: true, onUpdated: 'onPropUpdate')]
    public ?string $nom = null;

    #[LiveProp(writable: true, onUpdated: 'onPropUpdate')]
    public ?string $debut = null;

    #[LiveProp(writable: true)]
    public int $page = 1;

    #[LiveProp(writable: true)]
    public int $restaurantId;

    public function __construct(
        private RestaurantRepository $restaurantRepository,
        private PaginatorInterface $paginator,
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(CollaborateurRestaurantFiltreType::class, $this->createFilter());
    }

    public function onPropUpdate()
    {
        $this->page = 1;
    }

    #[LiveAction]
    public function changePage(): void
    {
    }

    public function getAffectations(): PaginationInterface
    {
        $query = $this->restaurantRepository->findCurrentAffectationsWithFilter(
            $this->restaurantId,
            $this->createFilter()
        );

        return $this->paginator->paginate(
            $query,
            $this->page,
            10
        );
    }

    private function createFilter(): CollaborateurRestaurantFiltre
    {
        $filter = new CollaborateurRestaurantFiltre();
        $filter->setFonction($this->fonction);
        $filter->setNom($this->nom);
        
        if ($this->debut) {
            try {
                $filter->setDebut(new DateTime($this->debut));
            } catch (\Exception $e) {
                $filter->setDebut(null);
            }
        }

        return $filter;
    }
}
