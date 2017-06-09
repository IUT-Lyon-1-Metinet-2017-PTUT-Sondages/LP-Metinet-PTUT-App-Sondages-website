<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 05/04/17
 * Time: 11:50
 */

namespace AppBundle\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsExistingVariantValidator extends ConstraintValidator
{
    /**
     * @var EntityManager
     */
    protected $em;


    /**
     * IsExistingVariantValidator constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * {@inheritdoc}
     * @param IsExistingVariant $constraint
     */
    public function validate($value, Constraint $constraint)
    {

        $variant = $this->em->getRepository('AppBundle:Variant')->findBy(['name' => $value->getName()]);
        if (!$variant) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
