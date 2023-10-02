<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class KeywordsTransformer implements DataTransformerInterface
{
    /**
     * @param $value
     *
     * @return mixed
     */
    public function transform($value)
    {
        return $value;
    }

    /**
     * @param string[] $value
     *
     * @return string[]
     */
    public function reverseTransform($value): array
    {
        $keywords = array_reduce(array_reverse($value), function ($keywords, $keyword) {
            if (empty($keywords) && $keyword === null) {
                return [];
            }

            $newKeyword = ($keyword === null) ? "" : $keyword;

            return [...$keywords, $newKeyword];
        }, []);

        return array_reverse($keywords);
    }
}
