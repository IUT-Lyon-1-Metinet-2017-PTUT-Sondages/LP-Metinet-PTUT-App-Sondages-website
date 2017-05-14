<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Answer
 *
 * @ORM\Table(name="answer")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AnswerRepository")
 */
class Answer
{
    /**
     * Many answers have One proposition.
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Proposition", inversedBy="answers")
     * @ORM\JoinColumn(name="proposition_id", referencedColumnName="id", onDelete="CASCADE")
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
     * @param string $sessionId
     * @return Answer
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * Get sessionIdd
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param mixed $proposition
     * @return $this
     */
    public function setProposition($proposition)
    {
        $this->proposition = $proposition;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProposition()
    {
        return $this->proposition;
    }
}
