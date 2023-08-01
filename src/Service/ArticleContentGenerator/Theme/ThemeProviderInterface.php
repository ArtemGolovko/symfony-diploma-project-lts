<?php

namespace App\Service\ArticleContentGenerator\Theme;

interface ThemeProviderInterface
{
    /**
     * @return Theme[]
     */
    public function getThemes(): array;

    /**
     * @return string[]
     */
    public function getThemeNames(): array;
}