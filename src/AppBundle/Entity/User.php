<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * @ExclusionPolicy("all")
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * One user has Many Polls.
     * @ORM\OneToMany(targetEntity="Poll", mappedBy="user", cascade={"persist", "remove"})
     */
    private $polls;

    /**
     * @Expose
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    protected $firstName;

    /**
     * @Expose
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    protected $lastName;

    /** {@inheritdoc} **/
    protected $password;

    public function __construct()
    {
        parent::__construct();
        $this->polls = new ArrayCollection();
    }

    /**
     * @param $email
     */
    private function fillNamesByEmail($email)
    {
        if (strpos($email, '@') === false) {
            return;
        }

        $parts = explode('@', $email);
        $username = $parts[0];
        $this->setUsername($username);

        if (strpos($username, '.') === false) {
            return;
        }

        list($first_name, $last_name) = explode('.', $username);
        $first_name = ucfirst(strtolower($first_name));
        $last_name = ucfirst(strtolower($last_name));
        $this->setFirstName($first_name);
        $this->setLastName($last_name);
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->fillNamesByEmail($email);
        parent::setEmail($email);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     *
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
     * @param mixed $lastName
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
     *
     * @param Poll $poll
     *
     * @return self
     */
    public function addPoll(Poll $poll)
    {
        $this->polls[] = $poll;

        return $this;
    }

    /**
     * Remove poll.
     *
     * @param Poll $poll
     */
    public function removePoll(Poll $poll)
    {
        $this->polls->removeElement($poll);
    }

    /**
     * Get polls.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPolls()
    {
        return $this->polls;
    }
}
