<?php

namespace App\Service\ArticleContentGenerator;

interface ThemeProviderInterface
{
    /**
     * @return Theme[]
     */
    function getThemes(): array;

    /**
     * @return string[]
     */
    function getThemeName(): array;
}