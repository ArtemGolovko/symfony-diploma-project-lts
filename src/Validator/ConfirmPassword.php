<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ConfirmPassword extends Constraint
{
    /**
     * @var string
     */
    public string $message = 'Пароли не совпадают';
}
