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
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="sessionId", type="string", length=255)
     */
    private $sessionId;

    /**
     * Many answers have One variant.
     * @ORM\ManyToOne(targetEntity="variant", inversedBy="answers")
     * @ORM\JoinColumn(name="variant_id", referencedColumnName="id")
     */
    private $variant;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

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
}
