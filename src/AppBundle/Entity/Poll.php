<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Table(name="Poll")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PollRepository")
 */
class Poll
{
    use ORMBehaviors\Sluggable\Sluggable,
        ORMBehaviors\SoftDeletable\SoftDeletable,
        ORMBehaviors\Timestampable\Timestampable
    ;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length = 120, name="title", nullable = false)
     * @Assert\NotBlank()
     * @var string
     */
    private $title;

    /**
     * @ORM\Column(type="text", length = 255, name="description", nullable = false)
     * @Assert\NotBlank()
     * @var string
     */
    private $description;

    /**
     * One Poll has Many Questions.
     * @ORM\OneToMany(targetEntity="Question", mappedBy="poll")
     */
    private $questions;

    /**
     * Many polls have One user.
     * @ORM\ManyToOne(targetEntity="user", inversedBy="polls")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    public function __construct() {
        $this->questions = new ArrayCollection();
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
     * @return Poll
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
     * Set description
     *
     * @param string $description
     *
     * @return Poll
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function getSluggableFields()
    {
        return [ 'title' ];
    }


    /**
     * Add question.
     *
     * @param \App\Entity\Question $question
     *
     * @return Brand
     */
    public function addQuestion(\App\Entity\Question $question)
    {
        $this->questions[] = $question;
        return $this;
    }
    /**
     * Remove question.
     *
     * @param \App\Entity\Question $question
     */
    public function removeQuestion(\App\Entity\Question $question)
    {
        $this->questions->removeElement($question);
    }
    /**
     * Get questions.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * Gets the Many polls have One user.
     *
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Sets the Many polls have One user.
     *
     * @param mixed $user the user
     *
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
}

