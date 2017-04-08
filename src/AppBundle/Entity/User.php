<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
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
     * @ORM\Column(type="string", length=100)
     */
    protected $first_name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $last_name;

    public function __construct()
    {
        parent::__construct();
        $this->polls = new ArrayCollection();
        // your own logic
    }

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

    public function setEmail($email)
    {
        $this->fillNamesByEmail($email);
        return parent::setEmail($email);
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param mixed $first_name
     * @return $this
     */
    private function setFirstName($first_name)
    {
        $this->first_name = $first_name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param mixed $last_name
     * @return $this
     */
    private function setLastName($last_name)
    {
        $this->last_name = $last_name;
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
