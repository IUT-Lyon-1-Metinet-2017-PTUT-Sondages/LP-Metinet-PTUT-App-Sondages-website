<?php

namespace AppBundle\Services;

use AppBundle\Entity\Variant;
use Doctrine\ORM\EntityManager;

/**
 * Class VariantRepositoryService
 * @package AppBundle\Services
 */
class VariantRepositoryService
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
     * @param array $filter
     * @return Variant[]|array
     */
    public function getVariants(array $filter = [])
    {
        return $this->em->getRepository('AppBundle:Variant')->findBy($filter);
    }

    /**
     * @param array $filter
     * @return Variant|null
     */
    public function getVariant(array $filter = [])
    {
        return $this->em->getRepository('AppBundle:Variant')->findOneBy($filter);
    }

    /**
     * @return null|Variant
     */
    public function getCheckboxType()
    {
        return $this->em->getRepository('AppBundle:Variant')->findOneBy(['name' => Variant::CHECKBOX]);
    }

    /**
     * @return null|Variant
     */
    public function getRadioType()
    {
        return $this->em->getRepository('AppBundle:Variant')->findOneBy(['name' => Variant::RADIO]);
    }

    /**
     * @return null|Variant
     */
    public function getLinearScaleType()
    {
        return $this->em->getRepository('AppBundle:Variant')->findOneBy(['name' => Variant::LINEAR_SCALE]);
    }
}
