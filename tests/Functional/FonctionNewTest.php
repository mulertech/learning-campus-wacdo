<?php

namespace App\Tests\Functional;

use App\DataFixtures\Entity\FakeUtilisateur;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FonctionNewTest extends WebTestCase
{
    public function testCreateNewFonctionWithoutParameters(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getUtilisateur());
        $client->request('GET', '/fonction/new');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'CREER UNE NOUVELLE FONCTION');
    }

    public function testCreateNewFonctionWithParameters(): void
    {
        $client = static::createClient();
        $client->enableProfiler();

        $client->loginUser($this->getUtilisateur());

        $crawler = $client->request('GET', '/fonction/new');

        $csrfToken = $crawler->filter('input[name="fonction[_token]"]')->attr('value');
        $client->request('POST', '/fonction/new', [
            'fonction' => [
                'intitule' => 'Cuisinier',
                '_token' => $csrfToken,
            ],
        ]);

        $session = $client->getRequest()->getSession();
        $flashBag = $session->getFlashBag();
        self::assertTrue($flashBag->has('success'));
        self::assertSame('La fonction a bien été créée.', $flashBag->get('success')[0]);
        self::assertResponseStatusCodeSame(303);
        self::assertResponseRedirects('/fonction');
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
