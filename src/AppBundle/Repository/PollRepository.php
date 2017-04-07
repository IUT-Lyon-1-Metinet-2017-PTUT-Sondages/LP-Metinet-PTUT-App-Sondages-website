<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class PollRepository
 * @package AppBundle\Repository
 */
class PollRepository extends EntityRepository
{
    public function findResultsFromPoll($id)
    {
        return $this->createQueryBuilder('p')
            ->select('q.id', 'q.title', 'pr.id', 'pr.title', 'COUNT(pr.id) as amount')
            ->innerJoin('p.questions', 'q')
            ->innerJoin('q.propositions', 'pr')
            ->innerJoin('pr.answers', 'a')
            ->groupBy('pr.id')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()->getResult();
    }
}
