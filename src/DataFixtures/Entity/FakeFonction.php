<?php

namespace App\DataFixtures\Entity;

use App\Entity\Fonction;
use Faker\Factory;

class FakeFonction
{
    public static function new(): Fonction
    {
        $faker = Factory::create('fr_FR');

        // Fonctions d'un fast-food
        $fonctions = [
            'Caissier',
            'Cuisinier',
            'Manager',
            'Livreur',
            'Agent de nettoyage',
            'Superviseur de salle',
            'Responsable des ressources humaines',
            'Chef de cuisine',
            'Assistant manager',
            'Responsable marketing',
        ];

        return new Fonction()->setIntitule($faker->randomElement($fonctions));
    }
}
