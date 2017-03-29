<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;

/**
 * Class PollRepositoryService
 * @package AppBundle\Services
 */
class PollRepositoryService
{
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function createPoll($poll, $user)
    {
            $poll->setUser($user);
            $this->em->persist($poll);
            $this->em->flush();
    }

    public function getPolls($filter)
    {
        return $this->em->getRepository('AppBundle:Poll')->findBy($filter);
    }

    public function getPoll($filter)
    {
        return $this->em->getRepository('AppBundle:Poll')->findOneBy($filter);
    }

    public function deleteById($id)
    {
        $poll = $this->em->getRepository('AppBundle:Poll')->findOneBy(['id'=>$id]);
        $this->em->remove($poll);
        $this->em->flush();

        return true;
    }
}
