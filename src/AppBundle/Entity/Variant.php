<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Variant
 *
 * @ORM\Table(name="variant")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VariantRepository")
 */
class Variant
{
    const RADIO = 'Radio';
    const CHECKBOX = 'Checkbox';
    const LINEAR_SCALE = 'LinearScale';

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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var Proposition[]
     * One Variant has Many propositions.
     * @ORM\OneToMany(targetEntity="Proposition", mappedBy="variant", cascade={"persist", "remove"})
     */
    private $propositions;

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
     * @return Proposition[]
     */
    public function getPropositions()
    {
        return $this->propositions;
    }

    /**
     * Set name
     * @param string $name
     * @return Variant
     */
    public function setName($name)
    {
        $this->name = trim($name);

        return $this;
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
