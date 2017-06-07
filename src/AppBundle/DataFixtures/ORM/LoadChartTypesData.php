<?php

namespace App\DataFixtures\ORM;

use AppBundle\Entity\ChartType;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Variant;

/**
 * Class LoadVariantData
 * @package App\DataFixtures\ORM
 */
class LoadChartTypesData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $chartTypes = [
            'Bar',
            'Pie',
        ];

        foreach ($chartTypes as $key => $chartType) {
            $thisChartType = new ChartType();
            $thisChartType->setTitle($chartType);
            $manager->persist($thisChartType);
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
