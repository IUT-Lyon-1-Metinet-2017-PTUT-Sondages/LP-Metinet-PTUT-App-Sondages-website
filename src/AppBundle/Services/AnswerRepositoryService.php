<?php

namespace AppBundle\Services;

class AnswerRepositoryService
{
    private $em;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function createAnswer($answer)
    {
        $this->em->persist($answer);
        $this->em->flush();
    }

    public function getAnswers($filter)
    {
        return $this->em->getRepository('AppBundle:Answer')->findBy($filter);
    }

    public function getAnswer($filter)
    {
        return $this->em->getRepository('AppBundle:Answer')->findOneBy($filter);
    }

    public function deleteById($id)
    {
        $answer = $this->em->getRepository('AppBundle:Answer')->findOneBy(['id'=>$id]);
        $this->em->remove($answer);
        $this->em->flush();
    }
}
