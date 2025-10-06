<?php

namespace App\Twig\Components;

use App\Entity\RestaurantFiltre;
use App\Form\RestaurantFiltreType;
use App\Repository\RestaurantRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent('restaurant_filtre_form')]
class RestaurantFiltreForm extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp(writable: true, onUpdated: 'onPropUpdate')]
    public ?string $nom = null;

    #[LiveProp(writable: true, onUpdated: 'onPropUpdate')]
    public ?string $codePostal = null;

    #[LiveProp(writable: true, onUpdated: 'onPropUpdate')]
    public ?string $ville = null;

    #[LiveProp(writable: true)]
    public int $page = 1;

    public function __construct(
        private RestaurantRepository $restaurantRepository,
        private PaginatorInterface $paginator
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        $filtre = new RestaurantFiltre();
        $filtre->setNom($this->nom);
        $filtre->setCodePostal($this->codePostal);
        $filtre->setVille($this->ville);

        return $this->createForm(RestaurantFiltreType::class, $filtre);
    }

    public function onPropUpdate()
    {
        $this->page = 1;
    }

    #[LiveAction]
    public function changePage(): void
    {
    }

    public function getRestaurants()
    {
        $filtre = new RestaurantFiltre();
        $filtre->setNom($this->nom);
        $filtre->setCodePostal($this->codePostal);
        $filtre->setVille($this->ville);

        $query = $this->restaurantRepository->findAllWithFilter($filtre);

        return $this->paginator->paginate(
            $query,
            $this->page,
            10
        );
    }
}
