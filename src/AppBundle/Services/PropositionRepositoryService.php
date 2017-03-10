<?php

namespace AppBundle\Services;

class PropositionRepositoryService
{
    private $em;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function createProposition($proposition)
    {
            $this->em->persist($proposition);
            $this->em->flush();

            return true;

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
            $proposition = $this->em->getRepository('AppBundle:Proposition')->findOneBy(['id'=>$id]);
            $this->em->remove($proposition);
            $this->em->flush();

            return true;

    }

}
