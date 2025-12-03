<?php

namespace App\Twig\Components;

use App\Entity\Collaborateur;
use App\Entity\CollaborateurAffectationFiltre;
use App\Entity\Fonction;
use App\Form\CollaborateurAffectationFiltreType;
use App\Repository\AffectationRepository;
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

#[AsLiveComponent('collaborateur_show')]
class CollaborateurShow extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp(writable: true, onUpdated: 'onPropUpdate')]
    public ?Fonction $fonction = null;

    #[LiveProp(writable: true, onUpdated: 'onPropUpdate')]
    public ?string $debut = null;

    #[LiveProp(writable: true)]
    public int $page = 1;

    #[LiveProp(writable: true)]
    public Collaborateur $collaborateur;

    public function __construct(
        private AffectationRepository $affectationRepository,
        private PaginatorInterface $paginator,
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(CollaborateurAffectationFiltreType::class, $this->createFilter());
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
        $query = $this->affectationRepository->findByCollaborateurWithFilter(
            $this->createFilter(),
            $this->collaborateur,
        );

        return $this->paginator->paginate(
            $query,
            $this->page,
            10
        );
    }

    private function createFilter(): CollaborateurAffectationFiltre
    {
        $filter = new CollaborateurAffectationFiltre();
        $filter->setFonction($this->fonction);
        
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
