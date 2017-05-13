<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 5/13/17
 * Time: 10:19 AM
 */

namespace AppBundle\Services;

use AppBundle\Entity\Page;
use AppBundle\Entity\Poll;
use AppBundle\Entity\Proposition;
use AppBundle\Entity\Question;
use Doctrine\ORM\EntityManager;
/**
 * Class DeletionService
 * @package AppBundle\Services
 */


class DeletionService
{
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function handleEntityDeletion($toDelete){
        if(isset($toDelete)) {
            foreach ($toDelete as $name => $arrayOfId) {
                foreach ($arrayOfId as $id) {
                    //TODO : Check why i get quotes in key of entity
                    $name          = preg_replace("/'/", "", $name);
                    $deletedEntity = $this->em->getRepository('AppBundle:'.$name)
                                              ->findOneBy(['id' => $id]);
                    if (null !== $deletedEntity) {
                        $this->em->remove($deletedEntity);
                        $this->em->flush();
                    }
                }
            }
        }
    }
}