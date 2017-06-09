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
        $sql = 'SELECT pa.id AS paId, q.id AS qId, q.title AS qTitle, pr.id AS propId, pr.title AS propTitle, COUNT(pr.id)
AS amount, LOWER(ct.title) AS ctTitle, v.name as qType FROM poll p
INNER JOIN question q ON q.poll_id = p.id
INNER JOIN page pa ON q.page_id = pa.id
INNER JOIN proposition pr ON pr.question_id = q.id
INNER JOIN answer a ON a.proposition_id = pr.id
INNER JOIN chart_type ct ON q.chart_type_id=ct.id
INNER JOIN variant v ON pr.variant_id=v.id
WHERE p.id = :id
GROUP BY pr.id';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll();

    }

    public function findNbAnsweredPoll($user)
    {
        $query = $this->createQueryBuilder('p')
            ->select('COUNT(DISTINCT(a.sessionId)) as nbAnsweredPoll')
            ->innerJoin('p.questions', 'q')
            ->innerJoin('q.propositions', 'pr')
            ->innerJoin('pr.answers', 'a');
        if (null !== $user) {
            $query->andWhere('p.user = :user')
                ->setParameter('user', $user);
        }

        return $query->getQuery()->getSingleScalarResult();
    }

    public function findBy(array $criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL, $hydrate = true)
    {
        $qb = $this->createQueryBuilder('p');

        foreach ($criteria as $column => $value) {
            $qb = $qb
                ->where($column . '= :param')
                ->setParameter('param', $value);
        }

        $qb = $qb->groupBy('p.id')
            ->add('orderBy', "{$orderBy[0]} {$orderBy[1]}")
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        if ($hydrate) {
            return $qb
                ->getQuery()
                ->getResults();
        }

        return $qb;
    }

    public function findDeletedBy(array $criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL, $hydrate = true)
    {
        $qb = $this->createQueryBuilder('p');

        foreach ($criteria as $column => $value) {
            $qb = $qb
                ->where($column . '= :param')
                ->setParameter('param', $value);
        }

        $qb = $qb->andWhere('p.deletedAt <= CURRENT_TIMESTAMP()');

        $qb = $qb->groupBy('p.id')
            ->add('orderBy', "{$orderBy[0]} {$orderBy[1]}")
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        if($hydrate) {
            return $qb
                ->getQuery()
                ->getResult();
        }

        return $qb;
    }

    public function findDeletedOneBy(array $criteria)
    {
        $polls = $this->createQueryBuilder('p');
        $polls = $polls->andWhere('p.deletedAt <= CURRENT_TIMESTAMP()');

        foreach ($criteria as $column => $value) {
            $polls = $polls
                ->where($column . '= :param')
                ->setParameter('param', $value);
        }

        return $polls->getQuery()->getOneOrNullResult();
    }
}
