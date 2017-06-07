<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\User;
use Tests\AppBundle\DomainTestCase;

class SecurityControllerTest extends DomainTestCase
{
    const BACKOFFICE_URI = 'http://localhost/backoffice';
    const LOGIN_URI = self::BACKOFFICE_URI . '/login';
    const LOGIN_BUTTON = 'Connexion';

    const INPUT_USERNAME = '_username';
    const INPUT_PASSWORD = '_password';

    public function testRenderLogin()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', self::LOGIN_URI);

        $this->assertEquals("Connexion", $crawler->filter('h1')->text());
        $this->assertEquals("Inscription", $crawler->filter('h2')->text());
    }

    public function testLoginWithEmptyValues()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', self::LOGIN_URI);
        $form = $crawler->selectButton(self::LOGIN_BUTTON)->form();

        $client->submit($form);
        $crawler = $client->followRedirect();

        $alertDanger = $crawler->filter('.alert-danger')->first();

        $this->assertEquals(self::LOGIN_URI, $client->getRequest()->getUri());
        $this->assertEquals("Identifiants invalides.", $alertDanger->text());
    }

    public function testLoginWithNotExistingUser()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', self::LOGIN_URI);
        $form = $crawler->selectButton(self::LOGIN_BUTTON)->form([
            self::INPUT_USERNAME => 'john@doe.com',
            self::INPUT_PASSWORD => 'password',
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();

        $alertDanger = $crawler->filter('.alert-danger')->first();

        $this->assertEquals(self::LOGIN_URI, $client->getRequest()->getUri());
        $this->assertEquals("Identifiants invalides.", $alertDanger->text());
    }

    public function testLoginWithExistingUser()
    {
        $userManager = $this->getContainer()->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setEmail('hugo.alliaume@etu.univ-lyon1.fr');
        $user->setPlainPassword('hugo');
        $user->setEnabled(true);
        $userManager->updateUser($user);

        $client = $this->createClient();
        $crawler = $client->request('GET', self::LOGIN_URI);
        $form = $crawler->selectButton(self::LOGIN_BUTTON)->form([
            self::INPUT_USERNAME => 'hugo.alliaume@etu.univ-lyon1.fr',
            self::INPUT_PASSWORD => 'hugo',
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertEquals(self::BACKOFFICE_URI, $client->getRequest()->getUri());
        $this->assertEquals(0, $crawler->filter('.alert-danger')->count());
    }
}
