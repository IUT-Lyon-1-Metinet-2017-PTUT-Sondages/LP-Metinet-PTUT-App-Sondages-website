<?php

namespace App\DataFixtures\ORM;

use AppBundle\Entity\ChartType;
use AppBundle\Entity\ValidDomain;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Variant;

/**
 * Class LoadVariantData
 * @package App\DataFixtures\ORM
 */
class LoadValidDomainData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $domains = [
            'etu.univ-lyon1.fr',
            'univ-lyon1.fr',
        ];

        foreach ($domains as $domain) {
            $validDomain = new ValidDomain();
            $validDomain->setDomain($domain);
            $manager->persist($validDomain);
        }
        
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 2;
    }
}
