<?php

namespace App\DataFixtures;

use App\DataFixtures\Entity\FakeCollaborateur;
use App\DataFixtures\Entity\FakeRestaurant;
use App\Entity\Affectation;
use App\Entity\Collaborateur;
use App\Entity\Fonction;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $intitulesFonction = ['Equipier polyvalent', 'Manager', 'Caissier', 'Cuisinier'];
        $fonctions = [];
        foreach ($intitulesFonction as $intituleFonction) {
            $fonction = new Fonction()->setIntitule($intituleFonction);

            $fonctions[] = $fonction;

            $manager->persist($fonction);
        }

        $restaurants = [];
        for ($i = 0; $i < 30; $i++) {
            $restaurant = FakeRestaurant::new();

            $restaurants[] = $restaurant;

            $manager->persist($restaurant);
        }

        $collaborateurs = [];
        for ($i = 0; $i < 300; $i++) {
            $collaborateur = FakeCollaborateur::new();

            $collaborateurs[] = $collaborateur;

            $manager->persist($collaborateur);
        }

        foreach ($collaborateurs as $collaborateur) {
            $debut = $faker->dateTimeBetween('-2 years');
            $fin = $faker->randomElement([null, $faker->dateTimeBetween($debut, '+1 year')]);
            $affectation = new Affectation()
                ->setCollaborateur($collaborateur)
                ->setFonction($faker->randomElement($fonctions))
                ->setRestaurant($faker->randomElement($restaurants))
                ->setDateDebut($debut)
                ->setDateFin($fin);

            $manager->persist($affectation);
        }

        $utilisateur = new Utilisateur()
            ->setEmail('admin@admin.fr')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword(password_hash('password', PASSWORD_BCRYPT));
        $manager->persist($utilisateur);

        $manager->flush();
    }
}
