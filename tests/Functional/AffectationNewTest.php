<?php

namespace App\Tests\Functional;

use App\DataFixtures\Entity\FakeCollaborateur;
use App\DataFixtures\Entity\FakeFonction;
use App\DataFixtures\Entity\FakeRestaurant;
use App\DataFixtures\Entity\FakeUtilisateur;
use App\Entity\Collaborateur;
use App\Entity\Fonction;
use App\Entity\Restaurant;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AffectationNewTest extends WebTestCase
{
    public function testCreateNewAffectationWithoutParameters(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getUtilisateur());

        $restaurant = $this->getRestaurant();
        $client->request('GET', '/affectation/new/' . $restaurant->getId());

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains(
            'h1',
            'AJOUTER UN NOUVEAU COLLABORATEUR AU RESTAURANT ' . strtoupper($restaurant->getNom())
        );
    }

    public function testCreateNewAffectationWithParameters(): void
    {
        $client = static::createClient();
        $client->enableProfiler();

        $client->loginUser($this->getUtilisateur());

        $restaurant = $this->getRestaurant();
        $crawler = $client->request('GET', '/affectation/new/' . $restaurant->getId());

        $csrfToken = $crawler->filter('input[name="affectation_to_restaurant[_token]"]')->attr('value');
        $client->request('POST', '/affectation/new/' . $restaurant->getId(), [
            'affectation_to_restaurant' => [
                'collaborateur' => $this->getCollaborateur()->getId(),
                'fonction' => $this->getFonction()->getId(),
                'dateDebut' => '2023-01-01',
                'dateFin' => null,
                '_token' => $csrfToken,
            ],
        ]);

        $session = $client->getRequest()->getSession();
        $flashBag = $session->getFlashBag();
        self::assertTrue($flashBag->has('success'));
        self::assertSame('L\'affectation a bien été créée.', $flashBag->get('success')[0]);
        self::assertResponseStatusCodeSame(303);
        self::assertResponseRedirects('/restaurant/affectations/' . $restaurant->getId());
    }

    public function getUtilisateur(): Utilisateur
    {
        $container = static::getContainer();
        $entityManager = $container->get('doctrine')->getManager();
        $utilisateur = FakeUtilisateur::new();
        $entityManager->persist($utilisateur);
        $entityManager->flush();
        return $utilisateur;
    }

    public function getRestaurant(): Restaurant
    {
        $container = static::getContainer();
        $entityManager = $container->get('doctrine')->getManager();
        $restaurant = FakeRestaurant::new();
        $entityManager->persist($restaurant);
        $entityManager->flush();
        return $restaurant;
    }

    public function getFonction(): Fonction
    {
        $container = static::getContainer();
        $entityManager = $container->get('doctrine')->getManager();
        $fonction = FakeFonction::new();
        $entityManager->persist($fonction);
        $entityManager->flush();
        return $fonction;
    }

    public function getCollaborateur(): Collaborateur
    {
        $container = static::getContainer();
        $entityManager = $container->get('doctrine')->getManager();
        $collaborateur = FakeCollaborateur::new();
        $entityManager->persist($collaborateur);
        $entityManager->flush();
        return $collaborateur;
    }
}
