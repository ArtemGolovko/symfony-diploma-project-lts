<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueUser extends Constraint
{
    /**
     * @var string
     */
    public string $message = 'The value "{{ value }}" is not valid.';

    /**
     * @var bool
     */
    public bool $allowYourself = false;
}
