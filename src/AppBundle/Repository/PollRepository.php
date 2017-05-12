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

    public function findFullObjectById($id)
    {
        return $this->createQueryBuilder('p')
            ->select(
                'p.id',
                'p.description',
                'p.title',
                'pages',
                'q',
                'pr',
                'v'
            )

            ->innerJoin('p.pages', 'pages')
            ->innerJoin('pages.questions', 'q')
            ->innerJoin('q.propositions', 'pr')
            ->innerJoin('pr.variant', 'v')
            ->groupBy('p.id')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()->getResult();
    }

    public function findNbAnsweredPoll()
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(DISTINCT(a.sessionId)) as nbAnsweredPoll')
            ->innerJoin('p.questions', 'q')
            ->innerJoin('q.propositions', 'pr')
            ->innerJoin('pr.answers', 'a')
            ->getQuery()->getSingleScalarResult();
    }
}
