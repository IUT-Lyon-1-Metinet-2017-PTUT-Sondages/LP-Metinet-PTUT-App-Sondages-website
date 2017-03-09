<?php
/**
 * Created by PhpStorm.
 * User: kocal
 * Date: 09/03/17
 * Time: 14:31
 */

namespace Tests\AppBundle\Repository;


use AppBundle\Entity\User;
use FOS\UserBundle\Model\UserManager;
use Tests\AppBundle\DomainTestCase;

class UserLoginTest extends DomainTestCase
{

    const LOGIN_ACTION_ROUTE = '/login';
    const LOGIN_ACTION_BUTTON = 'Connexion';

    const INPUT_USERNAME = '_username';
    const INPUT_PASSWORD = '_password';

    /**
     * @var UserManager
     */
    private $userRepository;

    protected function setUp()
    {
        parent::setUp();
        $this->userRepository = $this->getContainer()->get('fos_user.user_manager');
    }

    protected function createUser($enabled = false)
    {
        $user = new User();
        $user->setUsername('John');
        $user->setEmail('john@etu.univ-lyon1.fr');
        $user->setPlainPassword('foobar');
        $user->setEnabled($enabled);
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function test_with_empty_credentials()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', self::LOGIN_ACTION_ROUTE);
        $form = $crawler->selectButton(self::LOGIN_ACTION_BUTTON)->form();
        $client->submit($form);

        $client->followRedirect();
        $responseContent = $client->getResponse()->getContent();

        $this->assertContains('Identifiants invalides.', $responseContent);
    }

    public function test_with_invalid_credentials()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', self::LOGIN_ACTION_ROUTE);
        $form = $crawler->selectButton(self::LOGIN_ACTION_BUTTON)->form([
            self::INPUT_USERNAME => 'email@example.com',
            self::INPUT_PASSWORD => 'password',
        ]);
        $client->submit($form);

        $client->followRedirect();
        $responseContent = $client->getResponse()->getContent();

        $this->assertContains('Identifiants invalides.', $responseContent);
    }

    public function test_with_valid_credentials()
    {
        $user = $this->createUser(true);
        $this->assertCount(1, $this->userRepository->findUsers());

        $client = $this->createClient();
        $crawler = $client->request('GET', self::LOGIN_ACTION_ROUTE);
        $form = $crawler->selectButton(self::LOGIN_ACTION_BUTTON)->form([
            self::INPUT_USERNAME => $user->getEmail(),
            self::INPUT_PASSWORD => 'foobar'
        ]);
        $client->submit($form);

        $client->followRedirect();
        $responseContent = $client->getResponse()->getContent();

        $this->assertContains('Connecté en tant que John', $responseContent);
    }
}