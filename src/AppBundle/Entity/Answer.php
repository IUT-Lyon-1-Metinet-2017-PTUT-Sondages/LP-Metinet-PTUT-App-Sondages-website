<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
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
     * @ORM\Column(name="sessionid", type="string", length=255)
     */
    private $sessionid;

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
     * Set sessionid
     *
     * @param string $sessionid
     *
     * @return Answer
     */
    public function setSessionid($sessionid)
    {
        $this->sessionid = $sessionid;

        return $this;
    }

    /**
     * Get sessionid
     *
     * @return string
     */
    public function getSessionid()
    {
        return $this->sessionid;
    }
}

