<?php

namespace App\Service\ArticleContentGenerator;

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