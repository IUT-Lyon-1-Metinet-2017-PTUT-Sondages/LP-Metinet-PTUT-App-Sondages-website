<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\ValidDomain;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class IsValidDomainValidator
 * @package AppBundle\Validator\Constraints
 */
class IsValidDomainValidator extends ConstraintValidator
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * IsValidDomainValidator constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     * @param IsValidDomain $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var ValidDomain[] $validDomains */
        $validDomains = $this->entityManager->getRepository('AppBundle:ValidDomain')->findAll();

        foreach ($validDomains as $validDomain) {
            if (strpos($value, $validDomain->getDomain())) {
                return;
            }
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }
}
