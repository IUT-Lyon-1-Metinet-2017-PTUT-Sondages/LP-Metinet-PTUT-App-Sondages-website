<?php
// src/App/DataFixtures/ORM/LoadUserData.php
namespace App\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Question;
use AppBundle\Entity\Proposition;

class LoadQuestionData extends AbstractFixture implements OrderedFixtureInterface
{
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
    // the order in which fixtures will be loaded
    // the lower the number, the sooner that this fixture is loaded

    public function getOrder()
    {

        return 4;
    }

}
