<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 05/04/17
 * Time: 11:38
 */

namespace AppBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsExistingVariantType extends Constraint
{
    public $message = 'Variant must be an existing type of variant';
}