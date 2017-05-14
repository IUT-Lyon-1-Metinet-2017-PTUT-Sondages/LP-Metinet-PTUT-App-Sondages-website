<?php

namespace AppBundle\Services;

use AppBundle\Entity\Poll;
use AppBundle\Entity\User;
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
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Serializer
     */
    private $jms;

    /**
     * PollRepositoryService constructor.
     * @param EntityManager $entityManager
     * @param Serializer    $jms
     */
    public function __construct(EntityManager $entityManager, Serializer $jms)
    {
        $this->em = $entityManager;
        $this->jms = $jms;
    }

    /**
     * @param Poll $poll
     * @param User $user
     */
    public function createPoll(Poll $poll, User $user)
    {
        $poll->setUser($user);
        $this->em->persist($poll);
        $this->em->flush();
    }

    /**
     * @param array $filter
     * @return Poll[]|array
     */
    public function getPolls(array $filter = [])
    {
        return $this->em->getRepository('AppBundle:Poll')->findBy($filter);
    }

    /**
     * @param array $filter
     * @return null|Poll
     */
    public function getPoll(array $filter = [])
    {
        return $this->em->getRepository('AppBundle:Poll')->findOneBy($filter);
    }

    /**
     * @param int $id
     * @return array
     */
    public function getJsonPoll($id)
    {
        $serializationContext = SerializationContext::create()->setGroups(['backOffice']);

        $poll = $this->getPoll(['id' => $id]);
        $jsonPoll = $this->jms->serialize($poll, 'json', $serializationContext);

        return [$jsonPoll, $poll];
    }

    /**
     * @param Poll $poll
     */
    public function save(Poll $poll)
    {
        $this->em->persist($poll);
        $this->em->flush();
    }

    /**
     * @param int $id
     */
    public function deleteById($id)
    {
        $poll = $this->em->getRepository('AppBundle:Poll')->findOneBy(['id' => $id]);
        $this->em->remove($poll);
        $this->em->flush();
    }

    /**
     * @param int $id
     * @return array
     */
    public function getResults($id)
    {
        /** @var PollRepository $pollRepository */
        $pollRepository = $this->em->getRepository('AppBundle:Poll');

        return $pollRepository->findResultsFromPoll($id);
    }
}
