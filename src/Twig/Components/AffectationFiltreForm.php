<?php

namespace App\Twig\Components;

use App\Entity\AffectationFiltre;
use App\Entity\Fonction;
use App\Form\AffectationFiltreType;
use App\Repository\AffectationRepository;
use App\Repository\FonctionRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('affectation_filtre_form')]
class AffectationFiltreForm extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp(writable: true, onUpdated: 'onPropUpdate')]
    public ?string $ville = null;

    #[LiveProp(writable: true, onUpdated: 'onPropUpdate')]
    public ?string $debut = null;

    #[LiveProp(writable: true, onUpdated: 'onPropUpdate')]
    public ?string $fin = null;

    #[LiveProp(writable: true, onUpdated: 'onPropUpdate')]
    public ?Fonction $fonction = null;

    #[LiveProp(writable: true)]
    public int $page = 1;

    public function __construct(
        private AffectationRepository $affectationRepository,
        private FonctionRepository $fonctionRepository,
        private PaginatorInterface $paginator,
    )
    {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(AffectationFiltreType::class, $this->createFilter());
    }

    public function onPropUpdate()
    {
        $this->page = 1;
    }

    #[LiveAction]
    public function changePage(): void
    {
    }

    #[LiveAction]
    public function search(): void
    {
        $this->page = 1;
    }

    public function getAffectations()
    {
        $query = $this->affectationRepository->findAllWithFilter($this->createFilter());

        return $this->paginator->paginate(
            $query,
            $this->page,
            10
        );
    }

    private function createFilter(): AffectationFiltre
    {
        $filter = new AffectationFiltre();
        $filter->setVille($this->ville);
        
        if ($this->debut) {
            try {
                $filter->setDebut(new DateTime($this->debut));
            } catch (\Exception) {
                $filter->setDebut(null);
            }
        }
        
        if ($this->fin) {
            try {
                $filter->setFin(new DateTime($this->fin));
            } catch (\Exception) {
                $filter->setFin(null);
            }
        }
        
        $filter->setFonction($this->fonction);

        return $filter;
    }
}
