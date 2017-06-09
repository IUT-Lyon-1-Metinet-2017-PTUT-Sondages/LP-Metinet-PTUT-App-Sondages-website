<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as CustomAssert;

/**
 * @ExclusionPolicy("all")
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Poll[]
     * One user has Many Polls.
     * @ORM\OneToMany(targetEntity="Poll", mappedBy="user", cascade={"persist", "remove"})
     */
    private $polls;

    /**
     * @var string
     * @Expose
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    protected $firstName;

    /**
     * @inheritdoc
     * @CustomAssert\IsValidDomain()
     */
    protected $email;

    /**
     * @var string
     * @Expose
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    protected $lastName;

    /**
     * {@inheritdoc}
     */
    protected $password;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->polls = new ArrayCollection();
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        parent::setEmail($email);
        $parts = explode('@', $email);
        $username = $parts[0];
        $this->setUsername($username);

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Add poll.
     * @param Poll $poll
     * @return self
     */
    public function addPoll(Poll $poll)
    {
        $this->polls[] = $poll;

        return $this;
    }

    /**
     * Remove poll.
     * @param Poll $poll
     */
    public function removePoll(Poll $poll)
    {
        $this->polls->removeElement($poll);
    }

    /**
     * Get polls.
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPolls()
    {
        return $this->polls;
    }
}
