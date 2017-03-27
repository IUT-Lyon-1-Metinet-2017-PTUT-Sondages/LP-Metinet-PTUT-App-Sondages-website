<?php
// src/App/DataFixtures/ORM/LoadUserData.php
namespace App\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Poll;
class LoadPollData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {

            $thisPoll = new Poll();
            $thisPoll->setTitle('Premier sondage de test');
            $thisPoll->setDescription('Description de mon premier sondage t a vu');
            $questions = $manager->getRepository('AppBundle:Question')->findAll();
            foreach ($questions as $key => $question) {
               $thisPoll->addQuestion($question);
               $question->setPoll($thisPoll);
               $manager->persist($question);
            }
            $users = $manager->getRepository('AppBundle:User')->findAll();
            foreach ($users as $key => $user) {
               $thisPoll->setUser($user);
               $user->addPoll($thisPoll);
               $manager->persist($user);
            }
            $pages = $manager->getRepository('AppBundle:Page')->findAll();
            foreach ($pages as $key => $page) {
               $thisPoll->addPage($page);
               $page->setPoll($thisPoll);
               $manager->persist($page);
            }
            $manager->persist($thisPoll);
            $manager->flush();

    }
    // the order in which fixtures will be loaded
    // the lower the number, the sooner that this fixture is loaded

    public function getOrder()
    {

        return 6;
    }

}
