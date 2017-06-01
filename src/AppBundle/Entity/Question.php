<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Groups({"backOffice"})
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Groups({"backOffice"})
     * @ORM\Column(name="title", type="text", nullable=false)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var Poll
     * Many questions have One Poll.
     * @ORM\ManyToOne(targetEntity="Poll", inversedBy="questions")
     * @ORM\JoinColumn(name="poll_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $poll;

    /**
     * @var Page
     * Many questions have One Page.
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="questions")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $page;

    /**
     * @var Proposition[]
     * @Groups({"backOffice"})
     * One question has Many propositions.
     * @ORM\OneToMany(targetEntity="Proposition", mappedBy="question", cascade={"persist", "remove"})
     * @Assert\Valid
     */
    private $propositions;

    /**
     * Question constructor.
     */
    public function __construct()
    {
        $this->propositions = new ArrayCollection();
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
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = trim($title);

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
     * Add proposition.
     * @param Proposition $proposition
     * @return $this
     */
    public function addProposition(Proposition $proposition)
    {
        $this->propositions[] = $proposition;

        return $this;
    }

    /**
     * Remove proposition.
     * @param Proposition $proposition
     */
    public function removeProposition(Proposition $proposition)
    {
        $this->propositions->removeElement($proposition);
    }

    /**
     * Get propositions.
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPropositions()
    {
        return $this->propositions;
    }

    /**
     * @return Poll
     */
    public function getPoll()
    {
        return $this->poll;
    }

    /**
     * Sets the Poll
     * @param mixed $poll the poll
     * @return $this
     */
    public function setPoll(Poll $poll)
    {
        $this->poll = $poll;

        return $this;
    }

    /**
     * Gets the Page
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Sets the Page
     * @param Page $page the page
     * @return $this
     */
    public function setPage(Page $page)
    {
        $this->page = $page;

        return $this;
    }
}
