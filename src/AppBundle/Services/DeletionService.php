<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 5/13/17
 * Time: 10:19 AM
 */

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;

/**
 * Class DeletionService
 * @package AppBundle\Services
 */
class DeletionService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * DeletionService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param array $toDelete
     */
    public function handleEntityDeletion($toDelete)
    {
        if(is_array($toDelete)) {
            foreach ($toDelete as $name => $arrayOfId) {
                foreach ($arrayOfId as $id) {
                    $repository    = $this->em->getRepository('AppBundle:'.$name);
                    $deletedEntity = $repository->findOneBy(['id' => $id]);

                    if (!is_null($deletedEntity)) {
                        $this->em->remove($deletedEntity);
                        $this->em->flush();
                    }
                }
            }
        }
    }
}
