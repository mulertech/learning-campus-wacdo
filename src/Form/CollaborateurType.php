<?php

namespace App\Form;

use App\Entity\Collaborateur;
use App\Repository\UtilisateurRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollaborateurType extends AbstractType
{
    public function __construct(private readonly UtilisateurRepository $utilisateurRepository){}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom')
            ->add('nom')
            ->add('email', EmailType::class)
            ->add('datePremiereEmbauche')
            ->add('administrateur', CheckboxType::class, [
                'required' => false,
                'help' => 'Cochez pour donner les droits administrateur'
            ])
            ->add('submit', SubmitType::class, ['label' => 'Enregistrer'])
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $collaborateur = $event->getData();
            $form = $event->getForm();

            if ($collaborateur && $collaborateur->getEmail() && $collaborateur->isAdministrateur()) {
                $utilisateur = $this->utilisateurRepository->findOneBy(['email' => $collaborateur->getEmail()]);

                if (!$utilisateur) {
                    $form->get('administrateur')->addError(new FormError(
                        'Impossible de définir le collaborateur administrateur car sa fiche utilisateur n\'existe pas'
                    ));
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Collaborateur::class,
        ]);
    }
}
