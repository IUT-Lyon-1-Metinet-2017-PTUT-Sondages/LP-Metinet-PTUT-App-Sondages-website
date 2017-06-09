<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * Class IsValidDomain
 * @package AppBundle\Validator\Constraints
 */
class IsValidDomain extends Constraint
{
    public $message = 'valid_domain.validation.message';
}
