<?php

namespace App\Tests\Forms;

use App\DataFixtures\Entity\FakeCollaborateur;
use App\DataFixtures\Entity\FakeRestaurant;
use App\Entity\Affectation;
use App\Entity\Fonction;
use App\Form\AffectationCollaborateurToRestaurantType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AffectationCollaborateurToRestaurantTest extends KernelTestCase
{
    public function testValidForm(): void
    {
        $entityManager = self::getContainer()->get('doctrine')->getManager();

        $collaborateur = FakeCollaborateur::new();
        $entityManager->persist($collaborateur);
        $fonction = new Fonction()->setIntitule('fonction');
        $entityManager->persist($fonction);
        $restaurant = FakeRestaurant::new();
        $entityManager->persist($restaurant);

        $expectedAffectation = new Affectation()
            ->setDateDebut(new DateTime('2024-01-01'))
            ->setDateFin(new DateTime('2024-12-31'))
            ->setFonction($fonction)
            ->setCollaborateur($collaborateur)
            ->setRestaurant($restaurant);

        $affectation = new Affectation();
        $affectation->setCollaborateur($collaborateur);
        $affectation->setRestaurant($restaurant);
        $entityManager->flush();

        $form = self::getContainer()
            ->get('form.factory')
            ->create(AffectationCollaborateurToRestaurantType::class, $affectation, ['csrf_protection' => false]);

        $formData = [
            'dateDebut' => '2024-01-01',
            'dateFin' => '2024-12-31',
            'fonction' => $fonction->getId(),
        ];

        $form->submit($formData);

        $this->assertEquals($expectedAffectation, $affectation);
        $this->assertTrue($form->isValid());
    }

    public function testInvalidForm(): void
    {
        $entityManager = self::getContainer()->get('doctrine')->getManager();

        $collaborateur = FakeCollaborateur::new();
        $entityManager->persist($collaborateur);
        $fonction = new Fonction()->setIntitule('fonction');
        $entityManager->persist($fonction);
        $restaurant = FakeRestaurant::new();
        $entityManager->persist($restaurant);

        // Première affectation existante
        $firstAffectation = new Affectation()
            ->setDateDebut(new DateTime('2025-01-01'))
            ->setDateFin(new DateTime('2025-12-31'))
            ->setCollaborateur($collaborateur)
            ->setFonction($fonction)
            ->setRestaurant($restaurant);
        $entityManager->persist($firstAffectation);
        $entityManager->flush();

        // Nouvelle affectation avec des dates qui se chevauchent
        $affectation = new Affectation();
        $affectation->setCollaborateur($collaborateur);
        $affectation->setRestaurant($restaurant);

        $form = self::getContainer()
            ->get('form.factory')
            ->create(AffectationCollaborateurToRestaurantType::class, $affectation, ['csrf_protection' => false]);
        $formData = [
            'dateDebut' => '2025-08-01',
            'dateFin' => null,
            'fonction' => $fonction->getId(),
        ];
        $form->submit($formData);

        $this->assertFalse($form->isValid());
        $this->assertStringContainsString(
            'Impossible de définir le collaborateur à ce restaurant car il est déjà affecté à cette période',
            $form->getErrors(true)->__toString()
        );
    }
}
