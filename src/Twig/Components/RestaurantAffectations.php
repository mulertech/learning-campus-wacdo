<?php

namespace App\Twig\Components;

use App\Entity\CollaborateurRestaurantFiltre;
use App\Entity\Fonction;
use App\Form\CollaborateurRestaurantFiltreType;
use App\Repository\RestaurantRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('restaurant_affectations')]
class RestaurantAffectations extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?Fonction $fonction = null;

    #[LiveProp(writable: true)]
    public ?string $nom = null;

    #[LiveProp(writable: true)]
    public ?string $debut = null;

    #[LiveProp(writable: true)]
    public int $restaurantId;

    public function __construct(private RestaurantRepository $restaurantRepository) {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(CollaborateurRestaurantFiltreType::class, $this->createFilter());
    }

    public function getAffectations()
    {
        return $this->restaurantRepository->findAllAffectationsWithFilter(
            $this->restaurantId,
            $this->createFilter()
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
            } catch (\Exception) {
                $filter->setDebut(null);
            }
        }

        return $filter;
    }
}
