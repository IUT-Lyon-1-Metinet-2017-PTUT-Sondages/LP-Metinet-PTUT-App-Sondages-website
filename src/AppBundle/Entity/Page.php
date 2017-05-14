<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use JMS\Serializer\Annotation\Groups;

/**
 * Page
 *
 * @ORM\Table(name="page")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PageRepository")
 */
class Page
{
    /**
     * @var int
     * @Groups({"backOffice"})
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Groups({"backOffice"})
     * @ORM\Column(type="text", length = 255, name="title", nullable = true)
     * @ORM\Column(name="title", type="string", length = 255)
     */
    private $title;

    /**
     * @var Question[]
     * @Groups({"backOffice"})
     * One Poll has Many Questions.
     * @ORM\OneToMany(targetEntity="Question", mappedBy="page", cascade={"persist", "remove"})
     */
    private $questions;

    /**
     * @var string
     * @Groups({"backOffice"})
     * @ORM\Column(type="text", length = 255, name="description", nullable = true)
     */
    private $description;

    /**
     * @var Poll
     * Many pages have One Poll.
     * @ORM\ManyToOne(targetEntity="Poll", inversedBy="pages")
     * @ORM\JoinColumn(name="poll_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $poll;

    /**
     * Page constructor.
     */
    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

    /**
     * Get id
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     * @param string $title
     * @return Page
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add question.
     * @param Question $question
     * @return $this
     */
    public function addQuestion(Question $question)
    {
        $this->questions[] = $question;

        return $this;
    }

    /**
     * Get questions.
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * Remove question.
     * @param Question $question
     */
    public function removeQuestion(Question $question)
    {
        $this->questions->removeElement($question);
    }

    /**
     * Sets the Many pages have One Poll.
     * @param Poll $poll
     * @return $this
     */
    public function setPoll(Poll $poll)
    {
        $this->poll = $poll;

        return $this;
    }

    /**
     * Gets the Many pages have One Poll.
     * @return Poll
     */
    public function getPoll()
    {
        return $this->poll;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
