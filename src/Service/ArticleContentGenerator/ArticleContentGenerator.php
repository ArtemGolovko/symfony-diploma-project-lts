<?php

namespace App\Service\ArticleContentGenerator;

use App\Entity\ValueObject\ArticleGenerateOptions;
use App\Entity\ValueObject\Range;
use App\Service\ArticleContentGenerator\Module\ModuleInterface;
use App\Service\ArticleContentGenerator\Module\ModuleProviderInterface;
use App\Service\ArticleContentGenerator\Theme\Theme;
use App\Service\ArticleContentGenerator\Theme\ThemeChain;
use App\Service\ImageAssetService;
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
     * @var ImageAssetService
     */
    private ImageAssetService $asset;

    /**
     * @param ThemeChain              $themeProvider
     * @param ModuleProviderInterface $moduleProvider
     * @param ImageAssetService       $asset
     */
    public function __construct(
        ThemeChain $themeProvider,
        ModuleProviderInterface $moduleProvider,
        ImageAssetService $asset
    ) {
        $this->themeProvider = $themeProvider;
        $this->moduleProvider = $moduleProvider;

        $loader = new ArrayLoader([]);

        $twig = new Environment($loader);
        $twig->addExtension(new ArticleGeneratorExtension());

        $this->twig = $twig;
        $this->asset = $asset;
    }

    /**
     * @param ArticleGenerateOptions $options
     * @param bool                   $advancedFeatures
     *
     * @return string[]
     *
     * @throws LoaderError
     * @throws SyntaxError
     */
    public function generate(ArticleGenerateOptions $options, bool $advancedFeatures): array
    {
        $theme = $this->getTheme($options);

        $keyword = new KeywordWrapper($options->getKeywords(), $advancedFeatures);

        $modules = $this->getModules($options->getSize());
        $title = $this->generateTitle($theme, $options->getTitle(), $keyword);

        $paragraphsDistribution = $this->getParagraphsDistribution($modules);

        $paragraphs = new ParagraphsWrapper(
            $this->generateParagraphs($paragraphsDistribution, $theme, $options, $advancedFeatures)
        );
        $images = new ImagesWrapper($this->getImages($theme, $advancedFeatures ? $options->getImages() : []));

        $loader = new ArrayLoader([]);

        $twig = new Environment($loader, ['autoescape' => false]);
        $twig->addExtension(new ArticleGeneratorExtension());

        $modulesContext = [
            'title' => $title,
            'paragraphs' => $paragraphs,
            'paragraph' => $paragraphs,
            'imageSrc' => $images,
        ];

        $content = implode(
            '',
            array_map(function (ModuleInterface $module) use ($modulesContext, $twig) {
                $template = $twig->createTemplate($module->getTemplate());

                return $template->render($modulesContext);
            }, $modules)
        );

        return [
            'title' => $title,
            'content' => $content,
        ];
    }

    /**
     * @param string $title
     * @param string $keyword
     *
     * @return string
     * @throws LoaderError
     * @throws SyntaxError
     */
    public function generateDemo(string $title, string $keyword): string
    {
        $themeNames = $this->themeProvider->getThemeNames();
        $themeName = $themeNames[array_rand($themeNames)];

        $article = $this->generate(
            new ArticleGenerateOptions(
                $themeName,
                [$keyword],
                new Range(3, 6),
                [],
                $title
            ),
            false
        );

        return $article['content'];
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
    private function generateParagraphs(
        array $distribution,
        Theme $theme,
        ArticleGenerateOptions $options,
        bool $advancedFeatures
    ): array {
        $paragraphsAmount = array_sum($distribution);
        $paragraphs = [];
        $keyword = new KeywordWrapper($options->getKeywords(), $advancedFeatures);

        for ($i = 0; $i < $paragraphsAmount; $i++) {
            $index = array_rand($theme->paragraphs);
            $template = $this->twig->createTemplate($theme->paragraphs[$index]);
            $paragraphs[] = $template->render(['keyword' => $keyword]);
        }

        $promotedWords = $options->getPromotedWords();

        if (!$advancedFeatures) {
            array_splice($promotedWords, 1);
        }

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
            return array_map(function (string $filename) {
                return $this->asset->getImageUrl($filename);
            }, $images);
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
