<?php

namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Variant;

/**
 * Class LoadVariantData
 * @package App\DataFixtures\ORM
 */
class LoadVariantData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $variants = [
            'Radio',
            'Checkbox',
            'LinearScale',
        ];

        foreach ($variants as $key => $variant) {
            $thisVariant = new Variant();
            $thisVariant->setName($variant);
            $manager->persist($thisVariant);
            $manager->flush();
        }
    }
    // the order in which fixtures will be loaded
    // the lower the number, the sooner that this fixture is loaded

    public function getOrder()
    {
        return 2;
    }
}
