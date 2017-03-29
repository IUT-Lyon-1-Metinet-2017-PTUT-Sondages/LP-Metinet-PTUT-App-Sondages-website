<?php

namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Proposition;

/**
 * Class LoadPropositionData
 * @package App\DataFixtures\ORM
 */
class LoadPropositionData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $propositions=["prop1","prop2","prop3","prop4"];
        $variants = $manager->getRepository('AppBundle:Variant')->findAll();
        foreach ($propositions as $key => $proposition) {
            $thisProposition = new Proposition();
            $thisProposition->setTitle($proposition);
            $thisProposition->setVariant($variants[array_rand($variants)]);
            $manager->persist($thisProposition);
            $manager->flush();
        }
    }
    // the order in which fixtures will be loaded
    // the lower the number, the sooner that this fixture is loaded

    public function getOrder()
    {
        return 3;
    }
}
