<?php

namespace AppBundle\Services;

use AppBundle\Repository\PollRepository;
use Doctrine\ORM\EntityManager;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;
/**
 * Class PollRepositoryService
 * @package AppBundle\Services
 */
class PollRepositoryService
{
    private $em;
    private $jms;

    public function __construct(EntityManager $entityManager, Serializer $jms)
    {
        $this->em = $entityManager;
        $this->jms = $jms;
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

    public function getJsonPoll($id)
    {
//        $poll = $this->em->getRepository('AppBundle:Poll')->findFullObjectById($id);
        $poll = $this->getPoll(['id' => $id]);

        $jsonPoll = $this->jms->serialize(
            $poll,
            'json',
            SerializationContext::create()->setGroups(array('backOffice'))
        );

        return $jsonPoll;
    }

    public function deleteById($id)
    {
        $poll = $this->em->getRepository('AppBundle:Poll')->findOneBy(['id' => $id]);
        $this->em->remove($poll);
        $this->em->flush();
    }

    public function getResults($id)
    {
        /** @var PollRepository $pollRepository */
        $pollRepository = $this->em->getRepository('AppBundle:Poll');
        return $pollRepository->findResultsFromPoll($id);
    }
}
