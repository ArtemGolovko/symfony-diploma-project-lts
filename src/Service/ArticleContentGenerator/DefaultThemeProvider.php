<?php

namespace App\Service\ArticleContentGenerator;

class DefaultThemeProvider implements ThemeProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getThemes(): array
    {
        $theme = new Theme();

        $theme->name = 'Default Theme';
        $theme->paragraphs[] = 'Default paragraph about {{ word }}.';
        $theme->titles[] = 'Default title';
        $theme->images[] = 'https://placehold.co/600x400/EEE/31343C';

        return [$theme];
    }

    /**
     * @inheritDoc
     */
    public function getThemeNames(): array
    {
        return ['Default Theme'];
    }
}