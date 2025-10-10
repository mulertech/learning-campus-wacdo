<?php

namespace App\DataFixtures\Entity;

use App\Entity\Restaurant;
use Faker\Factory;

class FakeRestaurant
{
    public static function new(): Restaurant
    {
        $faker = Factory::create('fr_FR');

        return new Restaurant()
            ->setNom($faker->company())
            ->setAdresse($faker->streetAddress())
            ->setCodePostal($faker->postcode())
            ->setVille($faker->city());
    }
}
