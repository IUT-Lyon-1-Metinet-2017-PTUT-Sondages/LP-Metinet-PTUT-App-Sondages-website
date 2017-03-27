<?php

namespace AppBundle\Services;

class QuestionRepositoryService
{
    private $em;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function createQuestion($question)
    {
            $this->em->persist($question);
            $this->em->flush();

            return true;

    }

    public function getQuestions($filter)
    {
            return $this->em->getRepository('AppBundle:Question')->findBy($filter);


    }

    public function getQuestion($filter)
    {
            return $this->em->getRepository('AppBundle:Question')->findOneBy($filter);

    }


    public function deleteById($id)
    {
            $question = $this->em->getRepository('AppBundle:Question')->findOneBy(['id'=>$id]);
            $this->em->remove($question);
            $this->em->flush();

            return true;

    }

}
