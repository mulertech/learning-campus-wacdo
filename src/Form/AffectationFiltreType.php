<?php

namespace App\Form;

use App\Entity\AffectationFiltre;
use App\Entity\Fonction;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AffectationFiltreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ville')
            ->add('debut', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('fin', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('fonction', EntityType::class, [
                'class' => Fonction::class,
                'choice_label' => 'intitule',
                'placeholder' => 'Toutes les fonctions',
                'required' => false,
                'label' => 'Fonction'
            ])
            ->add('submit', SubmitType::class, ['label' => 'Enregistrer'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AffectationFiltre::class,
            'method' => 'GET',
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
