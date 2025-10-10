<?php

namespace App\DataFixtures\Entity;

use App\Entity\Collaborateur;
use Faker\Factory;

class FakeCollaborateur
{
    public static function new(): Collaborateur
    {
        $faker = Factory::create('fr_FR');

        $firstName = $faker->firstName();
        $firstNameEmail = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $firstName));
        $lastName = $faker->lastName();
        $lastNameEmail = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $lastName));

        return new Collaborateur()
            ->setPrenom($firstName)
            ->setNom($lastName)
            ->setEmail($firstNameEmail . '.' . $lastNameEmail . '@example.com')
            ->setDatePremiereEmbauche($faker->dateTimeBetween())
            ->setAdministrateur(false);
    }
}
