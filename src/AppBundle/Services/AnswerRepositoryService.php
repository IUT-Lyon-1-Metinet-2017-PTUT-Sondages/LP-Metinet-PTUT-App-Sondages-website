<?php

namespace AppBundle\Services;

use AppBundle\Entity\Answer;
use Doctrine\ORM\EntityManager;

class AnswerRepositoryService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * AnswerRepositoryService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param Answer $answer
     */
    public function createAnswer(Answer $answer)
    {
        $this->em->persist($answer);
        $this->em->flush();
    }

    /**
     * @param array $filter
     * @return Answer[]|array
     */
    public function getAnswers(array $filter = [])
    {
        return $this->em->getRepository('AppBundle:Answer')->findBy($filter);
    }

    /**
     * @param array $filter
     * @return Answer|null
     */
    public function getAnswer(array $filter = [])
    {
        return $this->em->getRepository('AppBundle:Answer')->findOneBy($filter);
    }

    /**
     * @param int $id
     */
    public function deleteById($id)
    {
        $answer = $this->em->getRepository('AppBundle:Answer')->findOneBy(['id' => $id]);
        $this->em->remove($answer);
        $this->em->flush();
    }
}
