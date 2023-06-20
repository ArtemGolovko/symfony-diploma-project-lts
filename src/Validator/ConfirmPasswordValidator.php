<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Should be used only in RepeatedPasswordExtension
 */
class ConfirmPasswordValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ConfirmPassword) {
            throw new UnexpectedTypeException($constraint, ConfirmPassword::class);
        }
        /* @var $constraint ConfirmPassword */

        $originalValue = $this
            ->context
            ->getObject()
            ->getParent()
            ->get('password')
            ->getData()
        ;

        if ($originalValue == $value) {
            return;
        }

        $this->context->buildViolation($constraint->message)
                      ->addViolation()
        ;
    }
}
