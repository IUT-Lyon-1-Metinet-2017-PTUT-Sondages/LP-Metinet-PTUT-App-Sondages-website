<?php

namespace AppBundle\Services;

use AppBundle\Entity\Poll;
use AppBundle\Entity\User;
use AppBundle\Repository\PollRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Events;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;
use Knp\DoctrineBehaviors\ORM\SoftDeletable\SoftDeletableSubscriber;

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
     * @param Serializer $serializer
     */
    public function __construct(EntityManager $entityManager, Serializer $serializer)
    {
        $this->em  = $entityManager;
        $this->jms = $serializer;
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
     * @param null $orderBy
     * @param bool $hydrate
     * @return Poll[]|array
     */
    public function getPolls(array $filter = [], $orderBy = null, $hydrate = true)
    {
        return $this->em->getRepository('AppBundle:Poll')->findBy($filter, $orderBy, null, null, $hydrate);
    }

    /**
     * @param array $filter
     * @param array $order
     * @param bool $hydrate
     * @return Poll[]|array
     */
    public function getArchivedPolls(array $filter = [], array $order, $hydrate = true)
    {
        $this->em->getFilters()->disable('softdeleteable');
        return $this->em->getRepository('AppBundle:Poll')->findDeletedBy($filter, $order, null, null, $hydrate);
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
     * @param array $filter
     * @return null|Poll
     */
    public function getArchivedPoll(array $filter = [])
    {
        $this->em->getFilters()->disable('softdeleteable');
        return $this->em->getRepository('AppBundle:Poll')->findDeletedOneBy($filter);
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
     * @param Poll $poll
     * @internal param int $id
     */
    public function delete(Poll $poll)
    {
        $this->em->remove($poll);
        $this->em->flush();
    }

    /**
     * @param int $id
     */
    public function hardDeleteById($id)
    {
        // initiate an array for the removed listeners
        $originalEventListeners = array();

        // cycle through all registered event listeners
        foreach ($this->em->getEventManager()->getListeners() as $eventName => $listeners) {
            foreach ($listeners as $listener) {
                if ($listener instanceof SoftDeletableSubscriber) {

                    // store the event listener, that gets removed
                    $originalEventListeners[Events::onFlush] = $listener;


                    // remove the SoftDeletableSubscriber event listener
                    $this->em->getEventManager()->removeEventListener($listener->getSubscribedEvents(), $listener);

                }
            }
        }
        $this->em->getFilters()->disable('softdeleteable');
        $poll = $this->em->getRepository('AppBundle:Poll')->findOneBy(['id' => $id]);

        // remove the entity
        $this->em->remove($poll);
        $this->em->flush();

        // re-add the removed listener back to the event-manager
        foreach ($originalEventListeners as $eventName => $listener) {
            $this->em->getEventManager()->addEventListener($eventName, $listener);
        }
        $this->em->getFilters()->enable('softdeleteable');


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
