<?php

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Question
 *
 * @ORM\Table(name="question")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\QuestionRepository")
 */
class Question
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
     * @ORM\Column(name="title", type="text", nullable=false)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * Many questions have One Poll.
     * @ORM\ManyToOne(targetEntity="Poll", inversedBy="questions")
     * @ORM\JoinColumn(name="poll_id", referencedColumnName="id")
     */
    private $poll;

    /**
     * One question has Many propositions.
     * @ORM\OneToMany(targetEntity="Proposition", mappedBy="question")
     */
    private $propositions;

    public function __construct() {
        $this->propositions = new ArrayCollection();
    }

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
     * Set title
     *
     * @param string $title
     *
     * @return Question
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }


    /**
     * Add proposition.
     *
     * @param \App\Entity\Proposition $proposition
     *
     * @return Brand
     */
    public function addProposition(\App\Entity\Proposition $proposition)
    {
        $this->propositions[] = $proposition;
        return $this;
    }
    /**
     * Remove proposition.
     *
     * @param \App\Entity\Proposition $proposition
     */
    public function removeProposition(\App\Entity\Proposition $proposition)
    {
        $this->propositions->removeElement($proposition);
    }
    /**
     * Get propositions.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPropositions()
    {
        return $this->propositions;
    }


    /**
     * Gets the Poll
     *
     * @return mixed
     */
    public function getPoll()
    {
        return $this->poll;
    }

    /**
     * Sets the Poll
     *
     * @param mixed $poll the poll
     *
     * @return self
     */
    public function setPoll($poll)
    {
        $this->poll = $poll;

        return $this;
    }
}

