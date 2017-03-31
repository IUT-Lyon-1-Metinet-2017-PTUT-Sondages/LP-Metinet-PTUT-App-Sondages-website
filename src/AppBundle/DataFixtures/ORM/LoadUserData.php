<?php

namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;

/**
 * Class LoadUserData
 * @package App\DataFixtures\ORM
 */
class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $userAdmin = new User();
        $userAdmin->setPlainPassword('richard');
        $userAdmin->setEmail('richard.raduly@etu.univ-lyon1.fr');
        $userAdmin->setEnabled(true);
        $manager->persist($userAdmin);
        $manager->flush();
    }
    // the order in which fixtures will be loaded
    // the lower the number, the sooner that this fixture is loaded
    
    public function getOrder()
    {
        return 1;
    }
}
