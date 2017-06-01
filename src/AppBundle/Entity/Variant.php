<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use JMS\Serializer\Annotation\Groups;

/**
 * Variant
 *
 * @ORM\Table(name="variant")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VariantRepository")
 */
class Variant
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
     * @Groups({"backOffice"})
     * @var string
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;


    /**
     *
     * One Variant has Many propositions.
     * @ORM\OneToMany(targetEntity="Proposition", mappedBy="variant", cascade={"persist", "remove"})
     */
    private $propositions;

    /**
     * @return mixed
     */
    public function getPropositions()
    {
        return $this->propositions;
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
     * Set name
     *
     * @param string $name
     *
     * @return Variant
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
