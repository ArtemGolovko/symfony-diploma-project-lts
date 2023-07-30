<?php

namespace App\Twig;

use App\Twig\Wrapper\KeywordWrapper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ArticleGeneratorExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('morph', [$this, 'morph']),
        ];
    }

    /**
     * @param KeywordWrapper $keyword
     * @param int            $form
     *
     * @return string
     */
    public function morph(KeywordWrapper $keyword, int $form): string
    {
        return $keyword->getForm($form);
    }
}