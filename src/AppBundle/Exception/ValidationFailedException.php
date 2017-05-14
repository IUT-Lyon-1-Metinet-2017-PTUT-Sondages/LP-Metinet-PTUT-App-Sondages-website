<?php
/**
 * Created by PhpStorm.
 * User: kocal
 * Date: 12/05/17
 * Time: 22:26
 */

namespace AppBundle\Exception;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationFailedException extends \Exception
{
    /**
     * @var array
     */
    private $errors = [];

    /**
     * ValidationFailedException constructor.
     * @param ConstraintViolationListInterface $errors
     */
    public function __construct(ConstraintViolationListInterface $errors)
    {
        foreach ($errors as $error) {
            /** @var ConstraintViolation $error */
            $root = $error->getRoot();
            $this->errors[] = [
                'message' => $error->getMessage(),
                'property' => $error->getPropertyPath(),
                'entityName' => strtolower((new \ReflectionClass($root))->getShortName()),
                'constraintName' => (new \ReflectionClass($error->getConstraint()))->getShortName(),
            ];
        }
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
