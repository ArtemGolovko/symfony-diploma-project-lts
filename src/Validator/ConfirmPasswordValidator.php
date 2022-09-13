<?php

namespace App\Validator;

use App\Form\RepeatedPasswordType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Should be used only in RepeatedPasswordType
 */
class ConfirmPasswordValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint ConfirmPassword */

        if (null === $value || '' === $value) {
            return;
        }

        /** @var FormType $confirmPassword */
        $repeatedPassword = $this->context->getObject()->getParent();
        $originalValue = $repeatedPassword->get('password')->getData();

        if ($originalValue == $value) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}
