<?php

namespace AppBundle\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class IsExistingVariantValidator
 * @package AppBundle\Validator\Constraints
 */
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
