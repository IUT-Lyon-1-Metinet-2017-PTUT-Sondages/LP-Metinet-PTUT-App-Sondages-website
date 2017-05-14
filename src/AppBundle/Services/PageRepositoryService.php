<?php

namespace AppBundle\Services;

use AppBundle\Entity\Page;
use Doctrine\ORM\EntityManager;

/**
 * Class PageRepositoryService
 * @package AppBundle\Services
 */
class PageRepositoryService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * PageRepositoryService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param Page $page
     */
    public function createPage(Page $page)
    {
        $this->em->persist($page);
        $this->em->flush();
    }

    /**
     * @param array $filter
     * @return Page[]|array
     */
    public function getPages(array $filter = [])
    {
        return $this->em->getRepository('AppBundle:Page')->findBy($filter);
    }

    /**
     * @param array $filter
     * @return Page|null
     */
    public function getPage(array $filter = [])
    {
        return $this->em->getRepository('AppBundle:Page')->findOneBy($filter);
    }

    /**
     * @param int $id
     */
    public function deleteById($id)
    {
        $page = $this->em->getRepository('AppBundle:Page')->findOneBy(['id' => $id]);
        $this->em->remove($page);
        $this->em->flush();
    }
}
