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
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $variants = [
            Variant::RADIO,
            Variant::CHECKBOX,
            Variant::LINEAR_SCALE,
        ];

        foreach ($variants as $key => $variant) {
            $thisVariant = new Variant();
            $thisVariant->setName($variant);
            $manager->persist($thisVariant);
            $manager->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 2;
    }
}
