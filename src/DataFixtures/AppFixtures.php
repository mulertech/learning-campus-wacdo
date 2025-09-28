<?php

namespace App\DataFixtures;

use App\Entity\Affectation;
use App\Entity\Collaborateur;
use App\Entity\Fonction;
use App\Entity\Restaurant;
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
            $restaurant = new Restaurant()
                ->setNom($faker->company())
                ->setAdresse($faker->address())
                ->setCodePostal($faker->postcode())
                ->setVille($faker->city());

            $restaurants[] = $restaurant;

            $manager->persist($restaurant);
        }

        $collaborateurs = [];
        for ($i = 0; $i < 300; $i++) {
            $firstName = $faker->firstName();
            $firstNameEmail = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $firstName));
            $lastName = $faker->lastName();
            $lastNameEmail = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $lastName));

            $collaborateur = new Collaborateur()
                ->setPrenom($firstName)
                ->setNom($lastName)
                ->setEmail($firstNameEmail . '.' . $lastNameEmail . '@example.com')
                ->setDatePremiereEmbauche($faker->dateTimeBetween())
                ->setAdministrateur(false);

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

        $manager->flush();
    }
}
