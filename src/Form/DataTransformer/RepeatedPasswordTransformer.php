<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class RepeatedPasswordTransformer implements DataTransformerInterface
{
    /**
     * @param string $value
     *
     * @return string[]
     */
    public function transform($value): array
    {
        return [
            'password' => $value,
            'confirmPassword' => $value,
        ];
    }

    /**
     * @param string[] $value
     *
     * @return string
     */
    public function reverseTransform($value): string
    {
        return $value['password'];
    }
}
