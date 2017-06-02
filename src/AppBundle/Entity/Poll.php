<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="poll")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PollRepository")
 * @ExclusionPolicy("all")
 */
class Poll
{
    use ORMBehaviors\Sluggable\Sluggable,
        ORMBehaviors\SoftDeletable\SoftDeletable,
        ORMBehaviors\Timestampable\Timestampable;
    /**
     * @var int
     * @Expose
     * @Groups({"Default", "backOffice"})
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Expose
     * @Groups({"Default", "backOffice"})
     * @ORM\Column(type="string", length = 120, name="title", nullable = false)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     * @Expose
     * @Groups({"Default", "backOffice"})
     * @ORM\Column(type="text", length = 255, name="description", nullable = false)
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @var Page[]
     * @Expose
     * @Groups({"Details", "backOffice"})
     * One Poll has Many Pages.
     * @ORM\OneToMany(targetEntity="Page", mappedBy="poll", cascade={"persist", "remove"})
     */
    private $pages;

    /**
     * @var Question[]
     * One Poll has Many Questions.
     * @ORM\OneToMany(targetEntity="Question", mappedBy="poll", cascade={"persist", "remove"})
     * @Assert\Valid
     */
    private $questions;

    /**
     * @var User
     * @Expose
     * @Groups({"User"})
     * Many polls have One user.
     * @ORM\ManyToOne(targetEntity="User", inversedBy="polls", fetch="EAGER")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @var string
     * @Expose
     * @ORM\Column(type="text", length = 255, name="access_token", nullable = false)
     */
    private $accessToken;

    /**
     * @var bool
     * @Expose
     * @ORM\Column(type="boolean", name="published")
     */
    private $published = false;

    /**
     * Poll constructor.
     */
    public function __construct()
    {
        $this->pages = new ArrayCollection();
        $this->questions = new ArrayCollection();

        $accessToken = '';
        $characterList = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($characterList, '8bit') - 1;
        for ($i = 0; $i < 8; ++$i) {
            $accessToken .= $characterList[random_int(0, $max)];
        }
        $this->accessToken = $accessToken;
    }

    /**
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
     * Set description
     * @param string $description
     * @return Poll
     */
    public function setDescription($description)
    {
        $this->description = trim($description);

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function getSluggableFields()
    {
        return ['title'];
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
     * Remove question.
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
     * Add page.
     * @param Page $page
     * @return self
     */
    public function addPage(Page $page)
    {
        $this->pages[] = $page;

        return $this;
    }

    /**
     * Remove page.
     *
     * @param Page $page
     */
    public function removePage(Page $page)
    {
        $this->pages->removeElement($page);
    }

    /**
     * Get pages.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * Gets user
     *
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Sets user
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

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     * @return $this
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * @return $this
     */
    public function publish()
    {
        $this->published = true;

        return $this;
    }
}
