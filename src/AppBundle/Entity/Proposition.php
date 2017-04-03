<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Proposition
 *
 * @ExclusionPolicy("all")
 * @ORM\Table(name="proposition")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PropositionRepository")
 */
class Proposition
{
    /**
     * @var int
     * @Expose
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Expose
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * Many propostions have One question.
     * @Expose
     * @ORM\ManyToOne(targetEntity="question", inversedBy="propositions")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $question;

    /**
     * One Proposition  has One Answer.
     * @Expose
     * @ORM\OneToOne(targetEntity="Answer", mappedBy="proposition")
     */
    private $answer;

    /**
     * Many propositions have One variant.
     * @ORM\ManyToOne(targetEntity="Variant", inversedBy="propositions")
     * @ORM\JoinColumn(name="variant_id", referencedColumnName="id")
     */
    private $variant;


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
     *
     * @return Proposition
     */
    public function setTitle($title)
    {
        $this->title = $title;

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
     * Gets the Many propostions have One question.
     * @return mixed
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Sets question
     * @param mixed $question the question
     *
     * @return self
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Gets the Many propositions have One variant.
     * @return mixed
     */
    public function getVariant()
    {
        return $this->variant;
    }

    /**
     * Sets the Many propositions have One variant.
     * @param mixed $variant the variant
     *
     * @return self
     */
    public function setVariant($variant)
    {
        $this->variant = $variant;

        return $this;
    }
    /**
     * @return mixed
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * @param mixed $answer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
    }
}
