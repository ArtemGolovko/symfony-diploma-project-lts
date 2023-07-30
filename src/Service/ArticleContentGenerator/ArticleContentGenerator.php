<?php

namespace App\Service\ArticleContentGenerator;

use App\Entity\ValueObject\ArticleGenerateOptions;
use App\Entity\ValueObject\Subscription;
use App\Twig\ArticleGeneratorExtension;
use App\Twig\Wrapper\KeywordWrapper;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Loader\ArrayLoader;

class ArticleContentGenerator
{
    /**
     * @var ThemeChain
     */
    private ThemeChain $themeProvider;

    /**
     * @param ThemeChain $themeProvider
     */
    public function __construct(ThemeChain $themeProvider)
    {
        $this->themeProvider = $themeProvider;
    }

    /**
     * @param ArticleGenerateOptions $options
     * @param Subscription           $subscription
     *
     * @return string
     *
     * @throws LoaderError
     * @throws SyntaxError
     * @throws \Exception
     */
    public function generate(ArticleGenerateOptions $options, Subscription $subscription): string
    {
        $theme = $this->getTheme($options);
        $paragraph = $this->getRandomParagraph($theme);

        $loader = new ArrayLoader([]);

        $twig = new Environment($loader);
        $twig->addExtension(new ArticleGeneratorExtension());

        $template = $twig->createTemplate($paragraph);

        return $template->render([
            'keyword' => new KeywordWrapper($options->getKeywords()),
        ]);
    }

    /**
     * @param $options
     *
     * @return Theme
     * @throws \Exception
     */
    private function getTheme($options): Theme
    {
        $themes = $this->themeProvider->getThemes();
        $themeNames = $this->themeProvider->getThemeNames();
        $themeName = $options->getTheme();

        if (!in_array($themeName, $themeNames)) {
            throw new \Exception('Theme not found');
        }

        $index = false;

        foreach ($themes as $key => $theme) {
            if ($theme->name === $themeName) {
                $index = $key;
                break;
            }
        }

        if (false === $index) {
            throw new \Exception('Theme not found');
        }

        return $themes[$index];
    }

    /**
     * @param Theme $theme
     *
     * @return string
     */
    private function getRandomParagraph(Theme $theme): string
    {
        $index = array_rand($theme->paragraphs);

        return $theme->paragraphs[$index];
    }
}