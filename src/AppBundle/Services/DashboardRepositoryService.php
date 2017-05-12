<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 10/05/17
 * Time: 13:12
 */

namespace AppBundle\Services;

use AppBundle\Repository\PollRepository;
use Doctrine\ORM\EntityManager;

/**
 * Class DashboardRepositoryService
 * @package AppBundle\Services
 */
class DashboardRepositoryService
{
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function getDashboardData($user)
    {
        $data = [];
        if ($user->hasRole('ROLE_ADMIN')) {
            $data['nbUsers'] = $this->getUsersData();
        }
        $data['nbPolls']   = $this->getPollData($user);
        $data['nbAnswers'] = $this->getAnswerData($user);

        return $data;
    }

    public function getUsersData()
    {

        return $this->em->getRepository('AppBundle:User')
            ->createQueryBuilder('u')
            ->select('COUNT(u.email) as nbUsers')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getPollData($user)
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

    public function getAnswerData($user)
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