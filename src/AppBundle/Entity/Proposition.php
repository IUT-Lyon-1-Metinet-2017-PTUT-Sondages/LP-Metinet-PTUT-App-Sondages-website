<?php

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AcmeAssert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;

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
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var Question
     * @Expose
     * @Groups({"Default"})
     * Many propositions have One question.
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="propositions")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $question;

    /**
     * @var Answer[]
     * One proposition has Many answers.
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="proposition", cascade={"persist", "remove"})
     */
    private $answers;

    /**
     * Many propositions have One variant.
     * @Expose
     * @Groups({"backOffice"})
     * @ORM\ManyToOne(targetEntity="Variant", inversedBy="propositions")
     * @ORM\JoinColumn(name="variant_id", referencedColumnName="id", onDelete="CASCADE")
     // @ AcmeAssert\IsExistingVariant
     // Type error: Argument 1 passed to AppBundle\Validator\Constraints\IsExistingVariantValidator::__construct() must be an instance of AppBundle\Services\VariantRepositoryService, none given, called in /home/kocal/Dev/Sites/LP-Metinet-PTUT-App-Sondages/website/vendor/symfony/symfony/src/Symfony/Bundle/FrameworkBundle/Validator/ConstraintValidatorFactory.php on line 77
     */
    private $variant;

    /**
     * Proposition constructor.
     */
    public function __construct()
    {
        $this->answers = new ArrayCollection();
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
     * Gets the Many propositions have One question.
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
     * @return $this
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
     * @return $this
     */
    public function setVariant($variant)
    {
        $this->variant = $variant;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Add answer.
     * @param Answer $answer
     * @return $this
     */
    public function addAnswer(Answer $answer)
    {
        $this->answers[] = $answer;

        return $this;
    }

    /**
     * Remove answer.
     * @param Answer $answer
     */
    public function removeAnswer(Answer $answer)
    {
        $this->answers->removeElement($answer);
    }
}
