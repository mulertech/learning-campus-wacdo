<?php

namespace App\Twig\Components;

use App\Entity\CollaborateurFiltre;
use App\Form\CollaborateurFiltreType;
use App\Repository\CollaborateurRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('collaborateur_filtre_form')]
class CollaborateurFiltreForm extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp(writable: true, onUpdated: 'onPropUpdate')]
    public ?string $prenom = null;

    #[LiveProp(writable: true, onUpdated: 'onPropUpdate')]
    public ?string $nom = null;

    #[LiveProp(writable: true, onUpdated: 'onPropUpdate')]
    public ?string $email = null;

    #[LiveProp(writable: true)]
    public int $page = 1;

    public function __construct(
        private CollaborateurRepository $collaborateurRepository,
        private PaginatorInterface $paginator
    )
    {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(CollaborateurFiltreType::class, $this->createFilter());
    }

    public function onPropUpdate()
    {
        $this->page = 1;
    }

    #[LiveAction]
    public function changePage(): void
    {
    }

    public function getCollaborateurs()
    {
        $query = $this->collaborateurRepository->findAllWithFilter($this->createFilter());

        return $this->paginator->paginate(
            $query,
            $this->page,
            10
        );
    }

    private function createFilter(): CollaborateurFiltre
    {
        $filter = new CollaborateurFiltre();
        $filter->setPrenom($this->prenom);
        $filter->setNom($this->nom);
        $filter->setEmail($this->email);

        return $filter;
    }
}
