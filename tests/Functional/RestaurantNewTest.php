<?php

namespace App\Tests\Functional;

use App\DataFixtures\Entity\FakeUtilisateur;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RestaurantNewTest extends WebTestCase
{
    public function testCreateNewRestaurantWithoutParameters(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getUtilisateur());
        $client->request('GET', '/restaurant/new');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'CREER UN NOUVEAU RESTAURANT');
    }

    public function testCreateNewRestaurantWithParameters(): void
    {
        $client = static::createClient();
        $client->enableProfiler();

        $client->loginUser($this->getUtilisateur());

        $crawler = $client->request('GET', '/restaurant/new');

        $csrfToken = $crawler->filter('input[name="restaurant[_token]"]')->attr('value');
        $client->request('POST', '/restaurant/new', [
            'restaurant' => [
                'nom' => 'Le Gourmet',
                'adresse' => '123 Rue de la Paix',
                'codePostal' => '75001',
                'ville' => 'Paris',
                '_token' => $csrfToken,
            ],
        ]);

        $session = $client->getRequest()->getSession();
        $flashBag = $session->getFlashBag();
        self::assertTrue($flashBag->has('success'));
        self::assertSame('Le restaurant a bien été créé.', $flashBag->get('success')[0]);
        self::assertResponseStatusCodeSame(303);
        self::assertResponseRedirects('/restaurant');
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
}
