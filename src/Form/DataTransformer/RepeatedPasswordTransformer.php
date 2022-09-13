<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class RepeatedPasswordTransformer implements DataTransformerInterface
{

    public function transform($value): array
    {
        return [
            'password' => $value,
            'confirmPassword' => $value
        ];
    }

    public function reverseTransform($value)
    {
        return $value['password'];
    }
}
