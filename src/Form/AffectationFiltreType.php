<?php

namespace App\Form;

use App\Entity\AffectationFiltre;
use App\Entity\Fonction;
use App\Entity\Restaurant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AffectationFiltreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('restaurant', EntityType::class, [
                'class' => Restaurant::class,
                'choice_label' => 'nom',
                'placeholder' => 'Tous les restaurants',
                'required' => false,
                'label' => 'Restaurant'
            ])
            ->add('debut', DateType::class, [
                'required' => false,
            ])
            ->add('fin', DateType::class, [
                'required' => false,
            ])
            ->add('fonction', EntityType::class, [
                'class' => Fonction::class,
                'choice_label' => 'intitule',
                'placeholder' => 'Toutes les fonctions',
                'required' => false,
                'label' => 'Fonction'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AffectationFiltre::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
