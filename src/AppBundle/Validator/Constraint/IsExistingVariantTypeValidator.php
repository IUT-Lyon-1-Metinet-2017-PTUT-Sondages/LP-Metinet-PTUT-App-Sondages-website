<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 05/04/17
 * Time: 11:50
 */

namespace AppBundle\Validator\Constraint;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsExistingVariantTypeValidator
{
    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function validate($value, Constraint $constraint)
    {
        $variants = $this->em->getRepository('AppBundle:Variant')->findAll();
        if (!in_array($value, $variants)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }


    }

}