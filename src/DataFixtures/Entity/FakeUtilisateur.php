<?php

namespace App\DataFixtures\Entity;

use App\Entity\Utilisateur;
use Faker\Factory;

class FakeUtilisateur
{
    public static function new(): Utilisateur
    {
        $faker = Factory::create('fr_FR');

        return new Utilisateur()
            ->setEmail($faker->email())
            ->setPassword('password')
            ->setRoles(["ROLE_ADMIN"]);
    }
}
