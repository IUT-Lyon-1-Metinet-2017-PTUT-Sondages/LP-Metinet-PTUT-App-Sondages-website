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
use Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tests\AppBundle\DomainTestCase;

class UserRegistrationTest extends DomainTestCase
{
    const REGISTRATION_ROUTE = '/register/';
    const REGISTRATION_BUTTON = 'Créer un compte';

    const MESSAGE_ENTER_EMAIL = "Entrez une adresse e-mail";
    const MESSAGE_ENTER_USERNAME = "Entrez un nom d'utilisateur";
    const MESSAGE_ENTER_PASSWORD = "Entrez un mot de passe";

    const ERROR_ALREADY_USED_EMAIL = "L'adresse e-mail est déjà utilisée.";
    const ERROR_ALREADY_USED_USERNAME = "Le nom d'utilisateur est déjà utilisé.";
    const ERROR_INVALID_EMAIL_DOMAIN = "L'adresse e-mail n'est pas associée à l'Université Lyon 1.";

    const INPUT_EMAIL = 'fos_user_registration_form[email]';
    const INPUT_USERNAME = 'fos_user_registration_form[username]';
    const INPUT_PASSWORD_FIRST = 'fos_user_registration_form[plainPassword][first]';
    const INPUT_PASSWORD_SECOND = 'fos_user_registration_form[plainPassword][second]';

    /**
     * @var UserManager
     */
    private $userRepository;

    protected function setUp()
    {
        parent::setUp();
        $this->userRepository = $this->getContainer()->get('fos_user.user_manager');
    }

    public function test_with_empty_values()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', self::REGISTRATION_ROUTE);
        $form = $crawler->selectButton(self::REGISTRATION_BUTTON)->form();
        $client->submit($form);

        $responseContent = $client->getResponse()->getContent();

        $this->assertContains($this->quote(self::MESSAGE_ENTER_EMAIL), $responseContent);
        $this->assertContains($this->quote(self::MESSAGE_ENTER_USERNAME), $responseContent);
        $this->assertContains($this->quote(self::MESSAGE_ENTER_PASSWORD), $responseContent);
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
            self::INPUT_EMAIL => 'john@etu.univ-lyon1.fr',
            self::INPUT_USERNAME => 'John',
            self::INPUT_PASSWORD_FIRST => 'foobar',
            self::INPUT_PASSWORD_SECOND => 'foobar',
        ));
        $client->submit($form);

        $responseContent = $client->getResponse()->getContent();

        $this->assertContains($this->quote(self::ERROR_ALREADY_USED_EMAIL), $responseContent);
        $this->assertContains($this->quote(self::ERROR_ALREADY_USED_USERNAME), $responseContent);
    }

    public function test_with_invalid_email_university_domain()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', self::REGISTRATION_ROUTE);
        $form = $crawler->selectButton(self::REGISTRATION_BUTTON)->form(array(
            self::INPUT_EMAIL => 'john@doe.com',
            self::INPUT_USERNAME => 'John',
            self::INPUT_PASSWORD_FIRST => 'foobar',
            self::INPUT_PASSWORD_SECOND => 'foobar',
        ));
        $client->submit($form);

        $responseContent = $client->getResponse()->getContent();

        $this->assertContains($this->quote(self::ERROR_INVALID_EMAIL_DOMAIN), $responseContent);
        $this->assertCount(0, $this->userRepository->findUsers());
    }

    public function test_with_valid_email_university_domain()
    {
        $client = $this->createClient();
        $client->enableProfiler();
        $crawler = $client->request('GET', self::REGISTRATION_ROUTE);
        $form = $crawler->selectButton(self::REGISTRATION_BUTTON)->form(array(
            self::INPUT_EMAIL => 'john@univ-lyon1.fr',
            self::INPUT_USERNAME => 'John',
            self::INPUT_PASSWORD_FIRST => 'foobar',
            self::INPUT_PASSWORD_SECOND => 'foobar',
        ));
        $client->submit($form);

        // Register user & send confirmation mail

        $profiler = $client->getProfile();
        /** @var MessageDataCollector $swiftMailerCollection */
        $swiftMailerCollection = $profiler->getCollector('swiftmailer');
        $client->followRedirect();
        $responseContent = $client->getResponse()->getContent();
        $john = $this->userRepository->findUserByUsername('John');

        $this->assertNotContains($this->quote(self::ERROR_INVALID_EMAIL_DOMAIN), $responseContent);
        $this->assertContains($this->quote("Un e-mail a été envoyé à l'adresse john@univ-lyon1.fr."), $responseContent);
        $this->assertCount(1, $this->userRepository->findUsers());
        $this->assertEquals('john@univ-lyon1.fr', $john->getEmail());
        $this->assertEquals('John', $john->getUsername());
        $this->assertFalse($john->isEnabled());
        $this->assertNotNull($john->getConfirmationToken());

        // Confirm by requesting the confirmation link

        $email = $swiftMailerCollection->getMessages()[0];
        $accountConfirmationLink = $this->getContainer()
            ->get('router')
            ->generate('fos_user_registration_confirm', [
                'token' => $john->getConfirmationToken()
            ], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->assertCount(1, $swiftMailerCollection->getMessages());
        $this->assertEquals("Bienvenue John !", $email->getSubject());
        $this->assertContains($accountConfirmationLink, $email->getBody());

        $client->request('GET', $accountConfirmationLink);
        $client->followRedirect();

        $this->userRepository->reloadUser($john);
        $responseContent = $client->getResponse()->getContent();

        $this->assertContains($this->quote("Félicitations John, votre compte est maintenant activé."), $responseContent);
        $this->assertTrue($john->isEnabled());
        $this->assertNull($john->getConfirmationToken());
    }
}
