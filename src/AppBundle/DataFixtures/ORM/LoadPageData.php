<?php
// src/App/DataFixtures/ORM/LoadUserData.php
namespace App\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Page;
class LoadPageData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {

            $thisPage = new Page();
            $thisPage->setTitle('Titre de la seule et unique page #PAGEUTILE');
            $questions = $manager->getRepository('AppBundle:Question')->findAll();
            foreach ($questions as $key => $question) {
               $thisPage->addQuestion($question);
               $question->setPage($thisPage);
               $manager->persist($question);
            }
            $manager->persist($thisPage);

            $manager->flush();

    }
    // the order in which fixtures will be loaded
    // the lower the number, the sooner that this fixture is loaded

    public function getOrder()
    {

        return 5;
    }

}
