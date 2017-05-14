<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 05/04/17
 * Time: 11:50
 */

namespace AppBundle\Validator\Constraints;

use AppBundle\Repository\VariantRepository;
use AppBundle\Services\VariantRepositoryService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsExistingVariantValidator extends ConstraintValidator
{
    /**
     * @var VariantRepository
     */
    private $variantRepositoryService;

    /**
     * IsExistingVariantValidator constructor.
     * @param VariantRepositoryService $variantRepositoryService
     */
    public function __construct(VariantRepositoryService $variantRepositoryService)
    {
        $this->variantRepositoryService = $variantRepositoryService;
    }

    /**
     * {@inheritdoc}
     * @param IsExistingVariant $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $variant = $this->variantRepositoryService->findBy(['name' => $value]);
        if (!$variant) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
