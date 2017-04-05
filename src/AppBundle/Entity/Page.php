<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

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
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     */
    private $title;

     /**
     * One Poll has Many Questions.
     * @ORM\OneToMany(targetEntity="Question", mappedBy="page")
     */
    private $questions;

    /**
     * @ORM\Column(type="text", length = 255, name="description", nullable = true)
     * @var string
     */
    private $description;

    /**
     * Many pages have One Poll.
     * @ORM\ManyToOne(targetEntity="Poll", inversedBy="pages")
     * @ORM\JoinColumn(name="poll_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $poll;

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
     * @return Page
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
     * Add question.
     *
     * @param Question $question
     *
     * @return self
     */
    public function addQuestion(Question $question)
    {
        $this->questions[] = $question;

        return $this;
    }
    /**
     * Remove question.
     *
     * @param Question $question
     */
    public function removeQuestion(Question $question)
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
     * Gets the Many pages have One Poll.
     *
     * @return mixed
     */
    public function getPoll()
    {
        return $this->poll;
    }

    /**
     * Sets the Many pages have One Poll.
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

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
}
