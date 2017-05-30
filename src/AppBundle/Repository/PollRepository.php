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
            ->select('q.id as qId', 'q.title as qTitle', 'pr.id as propId', 'pr.title as propTitle', 'COUNT(pr.id) as amount')
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
                    ->getQuery()
                    ->getResult();
    }

    public function findNbAnsweredPoll($user)
    {
       $query= $this->createQueryBuilder('p')
            ->select('COUNT(DISTINCT(a.sessionId)) as nbAnsweredPoll')
            ->innerJoin('p.questions', 'q')
            ->innerJoin('q.propositions', 'pr')
            ->innerJoin('pr.answers', 'a');
        if(null !== $user){
            $query->andWhere('p.user = :user')
                  ->setParameter('user', $user);
        }


        return $query->getQuery()->getSingleScalarResult();
    }
    public function findBy(array $criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL)
    {
        $polls = $this->createQueryBuilder('p');

        foreach($criteria as $column => $value){
            $polls = $polls->where($column, $value);
        }

        $polls = $polls->leftJoin('p.user', 'u')
            ->addSelect('u.email')
            ->groupBy('p.id')
            ->add('orderBy', "p.{$orderBy[0]} {$orderBy[1]}")
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return ($polls);
    }
}
