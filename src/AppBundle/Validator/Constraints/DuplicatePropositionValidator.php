<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 07/06/17
 * Time: 13:22
 */

namespace AppBundle\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DuplicatePropositionValidator extends ConstraintValidator
{
    /**
     * @var EntityManager
     */
    protected $em;


    /**
     * DuplicatePropositionValidator constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * {@inheritdoc}
     * @param IsExistingProposition $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value instanceof PersistentCollection) {
            foreach ($value as $prop) {
                $proposition = $this->em->getRepository('AppBundle:Proposition')->findOneBy(['title' => $prop->getTitle(), 'question' => $prop->getQuestion()]);
            }
        }
        if ($proposition) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}