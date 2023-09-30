<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class RepeatedPasswordTransformer implements DataTransformerInterface
{
    /**
     * @param string|null $value
     *
     * @return string|null[]
     */
    public function transform($value): array
    {
        return [
            'password' => $value,
            'confirmPassword' => $value,
        ];
    }

    /**
     * @param string|null[] $value
     *
     * @return string|null
     */
    public function reverseTransform($value): ?string
    {
        return $value['password'];
    }
}
