<?php

namespace App\Tests\Functional;

use App\DataFixtures\Entity\FakeUtilisateur;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CollaborateurNewTest extends WebTestCase
{
    public function testCreateNewCollaborateurWithoutParameters(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getUtilisateur());
        $client->request('GET', '/collaborateur/new');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'CREER UN NOUVEAU COLLABORATEUR');
    }

    public function testCreateNewCollaborateurWithParameters(): void
    {
        $client = static::createClient();
        $client->enableProfiler();

        $client->loginUser($this->getUtilisateur());

        $crawler = $client->request('GET', '/collaborateur/new');

        $csrfToken = $crawler->filter('input[name="collaborateur[_token]"]')->attr('value');
        $client->request('POST', '/collaborateur/new', [
            'collaborateur' => [
                'prenom' => 'John',
                'nom' => 'Doe',
                'email' => 'johndoe@example.com',
                'datePremiereEmbauche' => '2020-01-15',
                '_token' => $csrfToken,
            ],
        ]);

        $session = $client->getRequest()->getSession();
        $flashBag = $session->getFlashBag();
        self::assertTrue($flashBag->has('success'));
        self::assertSame('Le collaborateur a bien été créé.', $flashBag->get('success')[0]);
        self::assertResponseStatusCodeSame(303);
        self::assertResponseRedirects('/collaborateur');
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
