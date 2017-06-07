<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Controller\RegistrationController;
use AppBundle\Entity\User;
use Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tests\AppBundle\DomainTestCase;

class RegistrationControllerTest extends DomainTestCase
{
    const BACKOFFICE_URI = 'http://localhost/backoffice';
    const REGISTER_URI = self::BACKOFFICE_URI . '/register/';
    const REGISTER_LOGIN = 'Créer un compte';

    const INPUT_EMAIL = 'fos_user_registration_form[email]';
    const INPUT_PASSWORD = 'fos_user_registration_form[plainPassword][first]';
    const INPUT_PASSWORD_CONFIRMATION = 'fos_user_registration_form[plainPassword][second]';

    public function testRegisterFormRendering()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', self::REGISTER_URI);

        $this->assertEquals("Inscription", $crawler->filter('h1')->text());
        $this->assertEquals(
            "Adresse e-mail (Domaine de l'Université Lyon 1)",
            $crawler->filter('label[for=fos_user_registration_form_email]')->text()
        );
        $this->assertEquals(
            "Mot de passe",
            $crawler->filter('label[for=fos_user_registration_form_plainPassword_first]')->text()
        );
    }

    public function testRegisterWithEmptyValues()
    {
        $stub = $this->getMock(RegistrationController::class);
        $stub->method('checkGoogleRecaptcha')->willReturn(true);

        $client = $this->createClient();
        $crawler = $client->request('GET', self::REGISTER_URI);
        $form = $crawler->selectButton(self::REGISTER_LOGIN)->form([
            self::INPUT_EMAIL => '',
            self::INPUT_PASSWORD => '',
            self::INPUT_PASSWORD_CONFIRMATION => '',
        ]);

        $crawler = $client->submit($form);
        $content = $crawler->text();

        $this->assertCount(2, $crawler->filter('.has-danger'));
        $this->assertContains("Entrez une adresse e-mail s'il vous plait.", $content);
        $this->assertContains("Entrez un mot de passe s'il vous plait.", $content);
    }

    public function testRegisterWithBadEmailFormat()
    {
        $mock = $this->getMockBuilder(RegistrationController::class)->getMock();
        $mock->method('checkGoogleRecaptcha')->willReturn(true);

        $client = $this->createClient();
        $crawler = $client->request('GET', self::REGISTER_URI);
        $form = $crawler->selectButton(self::REGISTER_LOGIN)->form([
            self::INPUT_EMAIL => 'john@example.com',
            self::INPUT_PASSWORD => 'john',
            self::INPUT_PASSWORD_CONFIRMATION => 'john',
        ]);

        $crawler = $client->submit($form);
        $content = $crawler->text();

        $this->assertCount(1, $crawler->filter('.has-danger'));
        $this->assertContains("L'adresse e-mail n'a pas la forme « prenom.nom@...univ-lyon1.fr ».", $content);
    }

    public function testRegisterWithExistingUser()
    {
        $userManager = $this->getContainer()->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setEmail('hugo.alliaume@etu.univ-lyon1.fr');
        $user->setPlainPassword('hugo');
        $user->setEnabled(true);
        $userManager->updateUser($user);

        $client = $this->createClient();
        $crawler = $client->request('GET', self::REGISTER_URI);
        $form = $crawler->selectButton(self::REGISTER_LOGIN)->form([
            self::INPUT_EMAIL => 'hugo.alliaume@etu.univ-lyon1.fr',
            self::INPUT_PASSWORD => 'john',
            self::INPUT_PASSWORD_CONFIRMATION => 'john',
        ]);

        $crawler = $client->submit($form);

        $this->assertCount(1, $crawler->filter('.has-danger'));
        $this->assertContains("L'adresse e-mail est déjà utilisée.", $crawler->text());
    }

    public function testRegisterWithNotExistingUser()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', self::REGISTER_URI);
        $form = $crawler->selectButton(self::REGISTER_LOGIN)->form([
            self::INPUT_EMAIL => 'hugo.alliaume@etu.univ-lyon1.fr',
            self::INPUT_PASSWORD => 'john',
            self::INPUT_PASSWORD_CONFIRMATION => 'john',
        ]);

        $crawler = $client->submit($form);
        /** @var MessageDataCollector $swiftMailerCollection */
        $swiftMailerCollection = $client->getProfile()->getCollector('swiftmailer');
        $userManager = $this->getContainer()->get('fos_user.user_manager');
        $router = $this->getContainer()->get('router');

        $this->assertCount(0, $crawler->filter('.has-danger'));
        $this->assertCount(1, $swiftMailerCollection->getMessages());

        /** @var User $user */
        $user = $userManager->findUserByEmail('hugo.alliaume@etu.univ-lyon1.fr');
        $message = $swiftMailerCollection->getMessages()[0];
        $accountConfirmationLink = $router->generate('fos_user_registration_confirm', [
            'token' => $user->getConfirmationToken(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->assertFalse($user->isEnabled());
        $this->assertEquals('Hugo', $user->getFirstName());
        $this->assertEquals('Alliaume', $user->getLastName());
        $this->assertEquals('hugo.alliaume', $user->getUsername());

        $this->assertEquals("Bienvenue hugo.alliaume !", $message->getSubject());
        $this->assertContains($accountConfirmationLink, $message->getBody());

        // Activation de l'User
        $client->request('GET', $accountConfirmationLink);
        $crawler = $client->followRedirect();
        $userManager->reloadUser($user);

        $this->assertTrue($user->isEnabled());
        $this->assertContains("Félicitations hugo.alliaume, votre compte est maintenant activé.", $crawler->text());
    }
}
