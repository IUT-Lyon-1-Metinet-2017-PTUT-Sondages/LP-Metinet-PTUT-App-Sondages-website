<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 10/05/17
 * Time: 13:12
 */

namespace AppBundle\Services;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;

/**
 * Class DashboardRepositoryService
 * @package AppBundle\Services
 */
class DashboardRepositoryService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * DashboardRepositoryService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param User $user
     * @return array
     */
    public function getDashboardData(User $user)
    {
        $data = [];
        if ($user->hasRole('ROLE_ADMIN')) {
            $data['nbUsers'] = $this->getUsersData();
        }
        $data['nbPolls'] = $this->getPollData($user);
        $data['nbAnswers'] = $this->getAnswerData($user);

        return $data;
    }

    /**
     * @return mixed
     */
    public function getUsersData()
    {
        return $this->em->getRepository('AppBundle:User')
                        ->createQueryBuilder('u')
                        ->select('COUNT(u.email) as nbUsers')
                        ->getQuery()
                        ->getSingleScalarResult();
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function getPollData(User $user)
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return $this->em->getRepository('AppBundle:Poll')
                            ->createQueryBuilder('p')
                            ->select('COUNT(p.id) as nbPolls')
                            ->getQuery()
                            ->getSingleScalarResult();
        } else {
            return $this->em->getRepository('AppBundle:Poll')
                            ->createQueryBuilder('p')
                            ->select('COUNT(p.id) as nbPolls')
                            ->andWhere('p.user = :user')
                            ->setParameter('user', $user)
                            ->getQuery()
                            ->getSingleScalarResult();
        }
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function getAnswerData(User $user)
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return $this->em->getRepository('AppBundle:Poll')
                            ->findNbAnsweredPoll(null);
        } else {
            return $this->em->getRepository('AppBundle:Poll')
                            ->findNbAnsweredPoll($user);
        }
    }
}
