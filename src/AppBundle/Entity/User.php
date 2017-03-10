<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

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
        $this->setFirstName(ucfirst($first_name));
        $this->setLastName(ucfirst($last_name));
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
}