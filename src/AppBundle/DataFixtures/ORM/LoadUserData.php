<?php

namespace App\DataFixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadUserData
 * @package App\DataFixtures\ORM
 */
class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        // An admin
        $userAdmin = new User();
        $userAdmin->setEmail('richard.raduly@etu.univ-lyon1.fr');
        $userAdmin->setFirstName('Richard');
        $userAdmin->setLastName('Raduly');
        $userAdmin->setPlainPassword('richard');
        $userAdmin->addRole('ROLE_ADMIN');
        $userAdmin->setEnabled(true);
        $manager->persist($userAdmin);

        // An user
        $user = new User();
        $user->setEmail('hugo.alliaume@etu.univ-lyon1.fr');
        $user->setFirstName('Hugo');
        $user->setLastName('Alliaume');
        $user->setPlainPassword('hugo');
        $user->addRole('ROLE_USER');
        $user->setEnabled(true);
        $manager->persist($user);

        // A lot of users (add '/' before '/*' to enable this))
        /*
        $faker = \Faker\Factory::create('fr_FR');

        function normalize($name)
        {
            return strtolower(str_replace(' ', '', $name));
        }

        for ($i = 0; $i < 100; $i++) {
            $firstName = normalize($faker->firstName);
            $lastName = normalize($faker->lastName);
            $email = "$firstName.$lastName@etu.univ-lyon1.fr";

            $user = new User();
            $user->setEmail($email);
            $user->setPlainPassword($firstName);
            $user->addRole('ROLE_USER');
            $user->setEnabled(true);
            $manager->persist($user);
        }
        // */

        $manager->flush();
        $manager->clear();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
