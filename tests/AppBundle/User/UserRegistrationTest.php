<?php
/**
 * Created by PhpStorm.
 * User: lp
 * Date: 08/03/2017
 * Time: 12:21
 */

namespace Tests\AppBundle\Repository;


use AppBundle\Entity\User;
use FOS\UserBundle\Model\UserManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class UserRegistrationTest extends WebTestCase
{
    const REGISTRATION_ROUTE = '/register/';
    const REGISTRATION_BUTTON = 'Créer un compte';

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $em;

    /**
     * @var UserManager
     */
    private $userRepository;

    protected function setUp()
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $this->userRepository = $this->getContainer()->get('fos_user.user_manager');

        $this->runCommand('doctrine:schema:create', [], true);
        $this->runCommand('doctrine:schema:validate', [], true);
    }

    protected function tearDown()
    {
        $this->runCommand('doctrine:schema:drop', ['--force' => true], true);
    }

    private function quote($msg)
    {
        return htmlspecialchars($msg, ENT_QUOTES);
    }

    public function test_with_empty_values()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', self::REGISTRATION_ROUTE);
        $form = $crawler->selectButton(self::REGISTRATION_BUTTON)->form();
        $client->submit($form);

        $responseContent = $client->getResponse()->getContent();

        $this->assertContains($this->quote("Entrez une adresse e-mail"), $responseContent);
        $this->assertContains($this->quote("Entrez un nom d'utilisateur"), $responseContent);
        $this->assertContains($this->quote("Entrez un mot de passe"), $responseContent);
    }

    public function test_with_already_existing_user()
    {
        $user = new User();
        $user->setEmail('john@etu.univ-lyon1.fr');
        $user->setUsername('John');
        $user->setPlainPassword('foobar');
        $this->em->persist($user);
        $this->em->flush();

        $client = $this->createClient();
        $crawler = $client->request('GET', self::REGISTRATION_ROUTE);
        $form = $crawler->selectButton(self::REGISTRATION_BUTTON)->form(array(
            'fos_user_registration_form[email]' => 'john@etu.univ-lyon1.fr',
            'fos_user_registration_form[username]' => 'John',
            'fos_user_registration_form[plainPassword][first]' => 'foobar',
            'fos_user_registration_form[plainPassword][second]' => 'foobar',
        ));

        $client->submit($form);
        $responseContent = $client->getResponse()->getContent();

        $this->assertContains($this->quote("L'adresse e-mail est déjà utilisée."), $responseContent);
        $this->assertContains($this->quote("Le nom d'utilisateur est déjà utilisé."), $responseContent);
    }

    public function test_with_invalid_email_university_domain()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', self::REGISTRATION_ROUTE);
        $form = $crawler->selectButton(self::REGISTRATION_BUTTON)->form(array(
            'fos_user_registration_form[email]' => 'john@doe.com',
            'fos_user_registration_form[username]' => 'John',
            'fos_user_registration_form[plainPassword][first]' => 'foobar',
            'fos_user_registration_form[plainPassword][second]' => 'foobar',
        ));

        $client->submit($form);
        $responseContent = $client->getResponse()->getContent();

        $this->assertContains($this->quote("L'adresse e-mail n'est pas associée à l'Université Lyon 1."), $responseContent);
        $this->assertCount(0, $this->userRepository->findUsers());
    }

    public function test_with_valid_email_university_domain()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', self::REGISTRATION_ROUTE);
        $form = $crawler->selectButton(self::REGISTRATION_BUTTON)->form(array(
            'fos_user_registration_form[email]' => 'john@univ-lyon1.fr',
            'fos_user_registration_form[username]' => 'John',
            'fos_user_registration_form[plainPassword][first]' => 'foobar',
            'fos_user_registration_form[plainPassword][second]' => 'foobar',
        ));

        $client->submit($form);
        $responseContent = $client->getResponse()->getContent();

        $this->assertNotContains($this->quote("L'adresse e-mail n'est pas associée à l'Université Lyon 1."), $responseContent);
        $this->assertCount(1, $this->userRepository->findUsers());
        $this->assertEquals('john@univ-lyon1.fr', $this->userRepository->findUserByUsername('John')->getEmail());
    }
}
