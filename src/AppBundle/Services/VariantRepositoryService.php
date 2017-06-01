<?php

namespace AppBundle\Services;

use AppBundle\Entity\Variant;
use AppBundle\Repository\VariantRepository;
use Doctrine\ORM\EntityManager;

/**
 * Class VariantRepositoryService
 * @package AppBundle\Services
 */
class VariantRepositoryService
{
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function getVariants($filter = [])
    {
        return $this->em->getRepository('AppBundle:Variant')->findBy($filter);
    }

    public function getVariant($filter = [])
    {
        return $this->em->getRepository('AppBundle:Variant')->findOneBy($filter);
    }

    /**
     * @return null|Variant
     */
    public function getCheckboxType()
    {
        return $this->em->getRepository('AppBundle:Variant')->findOneBy(['name' => 'Checkbox']);
    }

    /**
     * @return null|Variant
     */
    public function getRadioType()
    {
        return $this->em->getRepository('AppBundle:Variant')->findOneBy(['name' => 'Radio']);
    }

    /**
     * @return null|Variant
     */
    public function getLinearScaleType()
    {
        return $this->em->getRepository('AppBundle:Variant')->findOneBy(['name' => 'LinearScale']);
    }
}
