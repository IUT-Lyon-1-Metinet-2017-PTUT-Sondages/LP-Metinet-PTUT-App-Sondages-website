<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;

/**
 * Class PropositionRepositoryService
 * @package AppBundle\Services
 */
class PropositionRepositoryService
{
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function createProposition($proposition)
    {
        $this->em->persist($proposition);
        $this->em->flush();
    }

    public function getPropositions($filter)
    {
        return $this->em->getRepository('AppBundle:Proposition')->findBy($filter);
    }

    public function getProposition($filter)
    {
        return $this->em->getRepository('AppBundle:Proposition')->findOneBy($filter);
    }


    public function deleteById($id)
    {
        $proposition = $this->em->getRepository('AppBundle:Proposition')->findOneBy(['id' => $id]);
        $this->em->remove($proposition);
        $this->em->flush();
    }
}
