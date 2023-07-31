<?php

namespace App\Service\ArticleContentGenerator;

use App\Entity\ValueObject\ArticleGenerateOptions;
use App\Entity\ValueObject\Range;
use App\Entity\ValueObject\Subscription;
use App\Twig\ArticleGeneratorExtension;
use App\Twig\Wrapper\KeywordWrapper;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Loader\ArrayLoader;
use Twig\Source;
use Twig\Token;

class ArticleContentGenerator
{
    /**
     * @var ThemeChain
     */
    private ThemeChain $themeProvider;

    /**
     * @var ModuleProviderInterface
     */
    private ModuleProviderInterface $moduleProvider;

    /**
     * @var Environment
     */
    private Environment $twig;

    /**
     * @param ThemeChain              $themeProvider
     * @param ModuleProviderInterface $moduleProvider
     */
    public function __construct(ThemeChain $themeProvider, ModuleProviderInterface $moduleProvider)
    {
        $this->themeProvider = $themeProvider;
        $this->moduleProvider = $moduleProvider;

        $loader = new ArrayLoader([]);

        $twig = new Environment($loader);
        $twig->addExtension(new ArticleGeneratorExtension());

        $this->twig = $twig;
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

        $keyword = new KeywordWrapper($options->getKeywords());

        $modules = $this->getModules($options->getSize());
        $title = $this->generateTitle($theme, $options->getTitle(), $keyword);

        $paragraphsAmount = $this->getParagraphsAmount($modules);
        $paragraphs = [];

        for ($i = 0; $i < $paragraphsAmount; $i++) {
            $paragraphs[] = $this->generateParagraph($theme, $keyword);
        }

        dd($title, $paragraphs);
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
     * @param Range $range
     *
     * @return ModuleInterface[]
     */
    private function getModules(Range $range): array
    {
        $modules = $this->moduleProvider->getModules();
        $indexes = array_rand($modules, $range->generate());

        $selectedModules = [];

        foreach ($indexes as $index) {
            $selectedModules[] = $modules[$index];
        }

        return $selectedModules;
    }

    /**
     * @param ModuleInterface[] $modules
     *
     * @return int
     * @throws SyntaxError
     */
    private function getParagraphsAmount(array $modules): int
    {
        $paragraphsAmount = 0;

        foreach ($modules as $module) {
            $tokens = $this->twig->tokenize(new Source($module->getTemplate(), 'module'));

            for (; !$tokens->isEOF(); $tokens->next()) {
                $token = $tokens->getCurrent();

                if ($token->test(Token::NAME_TYPE, 'paragraph')) {
                    $paragraphsAmount += 1;
                }

                if ($token->test(Token::NAME_TYPE, 'paragraphs')) {
                    $paragraphsAmount += mt_rand(1, 3);
                }
            }
        }

        return $paragraphsAmount;
    }

    /**
     * @param Theme          $theme
     * @param KeywordWrapper $keyword
     *
     * @return string
     * @throws LoaderError
     * @throws SyntaxError
     */
    private function generateParagraph(Theme $theme, KeywordWrapper $keyword): string
    {
        $index = array_rand($theme->paragraphs);
        $paragraph = $theme->paragraphs[$index];

        $template = $this->twig->createTemplate($paragraph);

        return $template->render([
            'keyword' => $keyword,
        ]);
    }

    /**
     * @param Theme                  $theme
     * @param ArticleGenerateOptions $options
     *
     * @return string
     * @throws LoaderError
     * @throws SyntaxError
     */
    private function generateTitle(Theme $theme, ?string $title, KeywordWrapper $keyword): string
    {
        if (null === $title) {
            $index = array_rand($theme->titles);

            $title = $theme->titles[$index];
        }

        $template = $this->twig->createTemplate($title);

        return $template->render([
            'keyword' => $keyword,
        ]);
    }
}