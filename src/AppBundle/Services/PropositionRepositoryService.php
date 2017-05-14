<?php

namespace AppBundle\Services;

use AppBundle\Entity\Proposition;
use Doctrine\ORM\EntityManager;

/**
 * Class PropositionRepositoryService
 * @package AppBundle\Services
 */
class PropositionRepositoryService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * PropositionRepositoryService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param Proposition $proposition
     */
    public function createProposition(Proposition $proposition)
    {
        $this->em->persist($proposition);
        $this->em->flush();
    }

    /**
     * @param array $filter
     * @return Proposition[]|array
     */
    public function getPropositions(array $filter = [])
    {
        return $this->em->getRepository('AppBundle:Proposition')->findBy($filter);
    }

    /**
     * @param array $filter
     * @return Proposition|null
     */
    public function getProposition(array $filter = [])
    {
        return $this->em->getRepository('AppBundle:Proposition')->findOneBy($filter);
    }

    /**
     * @param int $id
     */
    public function deleteById($id)
    {
        $proposition = $this->em->getRepository('AppBundle:Proposition')->findOneBy(['id' => $id]);
        $this->em->remove($proposition);
        $this->em->flush();
    }
}
