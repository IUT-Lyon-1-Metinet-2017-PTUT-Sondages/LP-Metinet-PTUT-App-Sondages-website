<?php

namespace AppBundle\Services;

use AppBundle\Entity\Question;
use Doctrine\ORM\EntityManager;

/**
 * Class QuestionRepositoryService
 * @package AppBundle\Services
 */
class QuestionRepositoryService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * QuestionRepositoryService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param Question $question
     */
    public function createQuestion(Question $question)
    {
        $this->em->persist($question);
        $this->em->flush();
    }

    /**
     * @param array $filter
     * @return Question[]|array
     */
    public function getQuestions(array $filter = [])
    {
        return $this->em->getRepository('AppBundle:Question')->findBy($filter);
    }

    /**
     * @param array $filter
     * @return Question|null
     */
    public function getQuestion(array $filter = [])
    {
        return $this->em->getRepository('AppBundle:Question')->findOneBy($filter);
    }

    /**
     * @param int $id
     */
    public function deleteById($id)
    {
        $question = $this->em->getRepository('AppBundle:Question')->findOneBy(['id' => $id]);
        $this->em->remove($question);
        $this->em->flush();
    }
}
