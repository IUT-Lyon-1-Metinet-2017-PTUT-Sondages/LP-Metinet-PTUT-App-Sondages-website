<?php
/**
 * Created by PhpStorm.
 * User: kocal
 * Date: 12/05/17
 * Time: 21:32
 */

namespace AppBundle\Exception;


use AppBundle\Entity\Question;
use Throwable;

class InvalidVariantException extends \Exception
{
    /**
     * @var string
     */
    private $variant;

    /**
     * @var Question
     */
    private $question;

    public function __construct(string $variant, Question $question)
    {
        $this->variant = $variant;
        $this->question = $question;
        parent::__construct(sprintf(
            "La variante « %s » n'est pas une variante valide.",
            $variant
        ));
    }

    /**
     * @return string
     */
    public function getVariant(): string
    {
        return $this->variant;
    }

    /**
     * @return Question
     */
    public function getQuestion(): Question
    {
        return $this->question;
    }
}