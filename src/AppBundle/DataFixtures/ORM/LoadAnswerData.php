<?php

namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Answer;

/**
 * Class LoadAnswerData
 * @package App\DataFixtures\ORM
 */
class LoadAnswerData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $propositions = $manager->getRepository('AppBundle:Proposition')->findAll();
        foreach ($propositions as $key => $proposition) {
            $thisAnswer = new Answer();
            $thisAnswer->setProposition($proposition);
            $thisAnswer->setSessionId(uniqid());
            $manager->persist($thisAnswer);
            $manager->flush();
        }
    }
    // the order in which fixtures will be loaded
    // the lower the number, the sooner that this fixture is loaded

    public function getOrder()
    {
        return 7;
    }
}
