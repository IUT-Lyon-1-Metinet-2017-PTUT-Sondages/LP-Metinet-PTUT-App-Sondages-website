<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 07/06/17
 * Time: 13:21
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DuplicateProposition extends Constraint
{
    public $message = 'Duplicate proposition';
}