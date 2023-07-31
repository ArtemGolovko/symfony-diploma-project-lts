<?php

namespace App\Service\ArticleContentGenerator;

use App\Entity\ValueObject\ArticleGenerateOptions;
use App\Entity\ValueObject\Range;
use App\Entity\ValueObject\Subscription;
use App\Twig\ArticleGeneratorExtension;
use App\Twig\Wrapper\ImagesWrapper;
use App\Twig\Wrapper\KeywordWrapper;
use App\Twig\Wrapper\ParagraphsWrapper;
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
     * @return string[]
     *
     * @throws LoaderError
     * @throws SyntaxError
     * @throws \Exception
     */
    public function generate(ArticleGenerateOptions $options, Subscription $subscription): array
    {
        $theme = $this->getTheme($options);

        $keyword = new KeywordWrapper($options->getKeywords());

        $modules = $this->getModules($options->getSize());
        $title = $this->generateTitle($theme, $options->getTitle(), $keyword);

        $paragraphsDistribution = $this->getParagraphsDistribution($modules);

        $paragraphs = new ParagraphsWrapper($this->generateParagraphs($paragraphsDistribution, $theme, $options));
        $images = new ImagesWrapper($this->getImages($theme, $options->getImages()));

        $loader = new ArrayLoader([]);

        $twig = new Environment($loader, ['autoescape' => false]);
        $twig->addExtension(new ArticleGeneratorExtension());

        $modulesContext = [
            'title' => $title,
            'paragraphs' => $paragraphs,
            'paragraph' => $paragraphs,
            'imageSrc' => $images,
        ];

        $article = implode(
            '',
            array_map(function (ModuleInterface $module) use ($modulesContext, $twig) {
                $template = $twig->createTemplate($module->getTemplate());

                return $template->render($modulesContext);
            }, $modules)
        );

        return [
            'title' => $title,
            'content' => $article,
        ];
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
        $modulesAmount = $range->generate();

        $selectedModules = [];

        for ($i = 0; $i < $modulesAmount; $i++) {
            $index = array_rand($modules);
            $selectedModules[] = $modules[$index];
        }

        return $selectedModules;
    }

    /**
     * @param ModuleInterface[] $modules
     *
     * @return int[]
     * @throws SyntaxError
     */
    private function getParagraphsDistribution(array $modules): array
    {
        $paragraphsDistribution = [];

        foreach ($modules as $module) {
            $tokens = $this->twig->tokenize(new Source($module->getTemplate(), 'module'));

            for (; !$tokens->isEOF(); $tokens->next()) {
                $token = $tokens->getCurrent();

                if ($token->test(Token::NAME_TYPE, 'paragraph')) {
                    $paragraphsDistribution[] = 1;
                }

                if ($token->test(Token::NAME_TYPE, 'paragraphs')) {
                    $paragraphsDistribution[] = random_int(1, 3);
                }
            }
        }

        return $paragraphsDistribution;
    }

    /**
     * @param int[]                  $distribution
     * @param Theme                  $theme
     * @param ArticleGenerateOptions $options
     *
     * @return string[][]
     * @throws SyntaxError
     * @throws LoaderError
     */
    private function generateParagraphs(array $distribution, Theme $theme, ArticleGenerateOptions $options): array
    {
        $paragraphsAmount = array_sum($distribution);
        $paragraphs = [];
        $keyword = new KeywordWrapper($options->getKeywords());

        for ($i = 0; $i < $paragraphsAmount; $i++) {
            $index = array_rand($theme->paragraphs);
            $template = $this->twig->createTemplate($theme->paragraphs[$index]);
            $paragraphs[] = $template->render(['keyword' => $keyword]);
        }

        $promotedWords = $options->getPromotedWords();

        foreach ($promotedWords as $promotedWord) {
            for ($i = 0; $i < $promotedWord->getRepetitions(); $i++) {
                $index = array_rand($paragraphs);

                $paragraphs[$index] = $this->injectPromotedWord($paragraphs[$index], $promotedWord);
            }
        }

        $paragraphs = array_reverse($paragraphs);
        $shapedParagraphs = [];

        foreach ($distribution as $key => $amount) {
            $shapedParagraphs[] = [];
            for ($i = 0; $i < $amount; $i++) {
                $shapedParagraphs[$key][] = array_pop($paragraphs);
            }
        }

        return $shapedParagraphs;
    }

    /**
     * @param string $paragraph
     * @param string $promotedWord
     *
     * @return string
     */
    private function injectPromotedWord(string $paragraph, string $promotedWord): string
    {
        $words = explode(' ', $paragraph);

        if (count($words) === 1) {
            return $promotedWord . ' ' . $paragraph;
        }

        $insetIndex = random_int(1, count($words) - 1);
        array_splice($words, $insetIndex, 0, $promotedWord);

        return implode(' ', $words);
    }

    /**
     * @param Theme          $theme
     * @param string|null    $title
     * @param KeywordWrapper $keyword
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

    /**
     * @param Theme $theme
     * @param array $images
     *
     * @return array
     */
    private function getImages(Theme $theme, array $images): array
    {
        if (count($images) > 0) {
            return $images;
        }

        $imagesAmount = random_int(1, 5);

        if (count($theme->images) <= $imagesAmount) {
            return $theme->images;
        }

        $indexes = array_rand($images, $imagesAmount);
        $selectedImages = [];

        foreach ($indexes as $index) {
            $selectedImages[] = $theme->images[$index];
        }

        return $selectedImages;
    }
}