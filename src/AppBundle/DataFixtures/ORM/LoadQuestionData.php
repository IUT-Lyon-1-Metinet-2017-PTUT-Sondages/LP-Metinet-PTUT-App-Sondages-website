<?php

namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Question;
use AppBundle\Entity\Proposition;

/**
 * Class LoadQuestionData
 * @package App\DataFixtures\ORM
 */
class LoadQuestionData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $thisQuestion = new Question();
        $thisQuestion->setTitle('Première question  de test');
        $propositions = $manager->getRepository('AppBundle:Proposition')->findAll();

        foreach ($propositions as $key => $proposition) {
            $thisQuestion->addProposition($proposition);
            $proposition->setQuestion($thisQuestion);
            $manager->persist($proposition);
        }

        $manager->persist($thisQuestion);
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 4;
    }
}
