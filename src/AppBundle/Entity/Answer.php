<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Answer
 *
 * @ORM\Table(name="answer")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AnswerRepository")
 */
class Answer
{
    /**
     * One answer has One proposition.
     * @ORM\OneToOne(targetEntity="Proposition")
     * @ORM\Id
     * @ORM\JoinColumn(name="proposition", referencedColumnName="id", onDelete="CASCADE")
     */
    private $proposition;



    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="sessionId", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $sessionId;

    /**
     * Set sessionIdd
     *
     * @param string $sessionId
     *
     * @return Answer
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * Get sessionIdd
     *
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }
    /**
     * @return mixed
     */
    public function getProposition()
    {
        return $this->proposition;
    }

    /**
     * @param mixed $proposition
     */
    public function setProposition($proposition)
    {
        $this->proposition = $proposition;
    }
}
