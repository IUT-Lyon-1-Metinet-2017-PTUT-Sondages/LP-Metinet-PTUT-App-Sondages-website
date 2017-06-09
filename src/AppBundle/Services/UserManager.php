<?php
namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;

class UserManager {

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function findUsersAndSortBy(array $orderBy = null) {
        return $this->em->getRepository('AppBundle:User')->findBy([], $orderBy);
    }
}
