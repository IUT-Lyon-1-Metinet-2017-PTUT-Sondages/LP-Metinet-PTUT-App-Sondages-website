<?php

namespace AppBundle\Services;

use AppBundle\Entity\Variant;
use Doctrine\ORM\EntityManager;

/**
 * Class VariantRepositoryService
 * @package AppBundle\Services
 */
class ChartTypeRepositoryService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * VariantRepositoryService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param array $criteria
     * @return \AppBundle\Entity\ChartType|null
     */
    public function findOneBy($criteria = []) {
        return $this->em->getRepository('AppBundle:ChartType')->findOneBy($criteria);
    }

    /**
     * @return \AppBundle\Entity\ChartType[]|array
     */
    public function all() {
        return $this->em->getRepository('AppBundle:ChartType')->findAll();
    }
}
