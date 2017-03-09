<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
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
     * @ORM\OneToMany(targetEntity="Poll", mappedBy="user")
     */
    private $polls;

    public function __construct()
    {
        parent::__construct();
        $this->polls = new ArrayCollection();
        // your own logic
    }

    /**
     * Add poll.
     *
     * @param \AppBundle\Entity\Poll $poll
     *
     * @return Brand
     */
    public function addPoll(\AppBundle\Entity\Poll $poll)
    {
        $this->polls[] = $poll;
        return $this;
    }
    /**
     * Remove poll.
     *
     * @param \AppBundle\Entity\Poll $poll
     */
    public function removePoll(\AppBundle\Entity\Poll $poll)
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
