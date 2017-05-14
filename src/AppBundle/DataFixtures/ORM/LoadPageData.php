<?php

namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Page;

/**
 * Class LoadPageData
 * @package App\DataFixtures\ORM
 */
class LoadPageData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 5;
    }
}
