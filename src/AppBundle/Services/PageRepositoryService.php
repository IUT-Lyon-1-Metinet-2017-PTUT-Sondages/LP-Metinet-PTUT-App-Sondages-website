<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;

/**
 * Class PageRepositoryService
 * @package AppBundle\Services
 */
class PageRepositoryService
{
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function createPage($page)
    {
            $this->em->persist($page);
            $this->em->flush();
    }

    public function getPages($filter)
    {
        return $this->em->getRepository('AppBundle:Page')->findBy($filter);
    }

    public function getPage($filter)
    {
        return $this->em->getRepository('AppBundle:Page')->findOneBy($filter);
    }

    public function deleteById($id)
    {
            $page = $this->em->getRepository('AppBundle:Page')->findOneBy(['id'=>$id]);
            $this->em->remove($page);
            $this->em->flush();
    }
}
