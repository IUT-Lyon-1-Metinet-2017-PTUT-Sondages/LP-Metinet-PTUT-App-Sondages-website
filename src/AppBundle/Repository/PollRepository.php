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
        $sql = 'SELECT q.id as qId, q.title as qTitle, pr.id as propId, pr.title as propTitle, COUNT(pr.id)
as amount, v.name as qType FROM poll p
INNER JOIN question q ON q.poll_id = p.id
INNER JOIN proposition pr ON pr.question_id = q.id
INNER JOIN answer a ON a.proposition_id = pr.id
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
}
