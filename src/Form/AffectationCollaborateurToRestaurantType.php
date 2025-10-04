<?php

namespace App\Form;

use App\Entity\Affectation;
use App\Entity\Fonction;
use App\Repository\AffectationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AffectationCollaborateurToRestaurantType extends AbstractType
{
    public function __construct(private readonly AffectationRepository $affectationRepository) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateDebut', options: [
                'label' => 'Date de début',
            ])
            ->add('dateFin', options: [
                'label' => 'Date de fin',
            ])
            ->add('fonction', EntityType::class, [
                'class' => Fonction::class,
                'choice_label' => 'intitule',
            ])
            ->add('submit', SubmitType::class, ['label' => 'Enregistrer'])
        ;

        // Vérifier si le collaborateur est déjà affecté à la même période
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $affectation = $event->getData();
            $form = $event->getForm();

            if ($affectation && $affectation->getDateDebut() && $affectation->getCollaborateur()) {
                $isAffecte = $this->affectationRepository->isCollaborateurAffecte($affectation);

                if ($isAffecte) {
                    $form->get('dateDebut')->addError(new FormError(
                        'Impossible de définir le collaborateur à ce restaurant car il est déjà affecté à cette période'
                    ));
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Affectation::class,
        ]);
    }
}
