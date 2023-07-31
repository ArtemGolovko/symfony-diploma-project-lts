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
use Twig\TokenStream;

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

        $paragraphsDistribution = $this->getParagraphsDistribution($modules);

        $paragraphs = $this->generateParagraphs($paragraphsDistribution, $theme, $options);

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
                    $paragraphsDistribution[] = mt_rand(1, 3);
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
        /** @var TokenStream[] $paragraphs */
        $paragraphs = [];

        for ($i = 0; $i < $paragraphsAmount; $i++) {
            $index = array_rand($theme->paragraphs);
            $paragraph = $theme->paragraphs[$index];
            $paragraphs[] = $this->twig->tokenize(new Source($paragraph, 'paragraph'));
        }

        $promotedWords = $options->getPromotedWords();
        $paragraphContext = [];

        foreach ($promotedWords as $key => $promotedWord) {
            $paragraphContext['promoted_word_' . $key] = $promotedWord->getWord();

            for ($i = 0; $i < $promotedWord->getRepetitions(); $i++) {
                $index = array_rand($paragraphs);

                $paragraphs[$index] = $this->injectPromotedWord(
                    $paragraphs[$index],
                    'promoted_word_' . $key
                );
            }
        }
        $renderedParagraphs = [];
        $paragraphContext['keyword'] = new KeywordWrapper($options->getKeywords());

        foreach ($paragraphs as $paragraph) {
            $paragraphTemplate = $this->recompileTokens($paragraph);
            $template = $this->twig->createTemplate($paragraphTemplate);

            $renderedParagraphs[] = $template->render($paragraphContext);
        }

        $renderedParagraphs = array_reverse($renderedParagraphs);
        $shapedParagraphs = [];

        foreach ($distribution as $key => $amount) {
            $shapedParagraphs[] = [];
            for ($i = 0; $i < $amount; $i++) {
                $shapedParagraphs[$key][] = array_pop($renderedParagraphs);
            }
        }

        return $shapedParagraphs;
    }

    /**
     * @param TokenStream $paragraph
     * @param string      $wordPlaceholder
     *
     * @return TokenStream
     * @throws SyntaxError
     */
    private function injectPromotedWord(TokenStream $paragraph, string $wordPlaceholder): TokenStream
    {
        /** @var Token[] $tokens */
        $tokens = [];
        $textTokenIndexes = [];

        for (; !$paragraph->isEOF(); $paragraph->next()) {
            $tokens[] = $paragraph->getCurrent();
        }

        $tokens[] = $paragraph->getCurrent();

        foreach ($tokens as $index => $token) {
            if ($token->test(Token::TEXT_TYPE)) {
                $textTokenIndexes[] = $index;
            }
        }

        $index = array_rand($textTokenIndexes);
        $textTokenIndex = $textTokenIndexes[$index];

        $textToken = $tokens[$textTokenIndex];

        $words = explode(' ', $textToken->getValue());

        $wordIndex = array_rand($words);
        $wordsBefore = [];
        $wordsAfter = [];

        foreach ($words as $index => $word) {
            if ($index <= $wordIndex) {
                $wordsBefore[] = $word;
                continue;
            }

            $wordsAfter[] = $word;
        }

        $textBefore = implode(' ', $wordsBefore) . ' ';

        $textAfter = ' ' . implode(' ', $wordsAfter);

        $tokensBefore = array_slice($tokens, 0, $textTokenIndex);
        $tokensAfter = array_slice($tokens, $textTokenIndex + 1);

        $lineno = $textToken->getLine();

        $insertTokens = [
            new Token(Token::TEXT_TYPE, $textBefore, $lineno),
            new Token(Token::VAR_START_TYPE, '', $lineno),
            new Token(Token::NAME_TYPE, $wordPlaceholder, $lineno),
            new Token(Token::VAR_END_TYPE, '', $lineno),
            new Token(Token::TEXT_TYPE, $textAfter, $lineno),
        ];

        $insertTokens = array_filter($insertTokens, function (Token $token) {
            return !$token->test(Token::TEXT_TYPE, '');
        });

        $newTokens = array_merge($tokensBefore, $insertTokens, $tokensAfter);

        return new TokenStream($newTokens);
    }

    /**
     * @param TokenStream $tokens
     *
     * @return string
     * @throws SyntaxError
     */
    private function recompileTokens(TokenStream $tokens): string
    {
        $token_array = [];

        for (; !$tokens->isEOF(); $tokens->next()) {
            $token_array[] = $tokens->getCurrent();
        }

        return implode(
            '',
            array_map(function (Token $token) {
                switch ($token->getType()) {
                    case Token::TEXT_TYPE:
                    case Token::NAME_TYPE:
                    case Token::NUMBER_TYPE:
                        return $token->getValue();
                    case Token::BLOCK_START_TYPE:
                        return '{% ';
                    case Token::VAR_START_TYPE:
                        return '{{ ';
                    case Token::BLOCK_END_TYPE:
                        return ' %}';
                    case Token::VAR_END_TYPE:
                        return ' }}';
                    case Token::STRING_TYPE:
                        return "'" . $token->getValue() . "'";
                    case Token::OPERATOR_TYPE:
                        return ' ' . $token->getValue() . ' ';
                    case Token::PUNCTUATION_TYPE:
                        return $token->getValue() . ' ';
                    case Token::INTERPOLATION_START_TYPE:
                        return '#{';
                    case Token::INTERPOLATION_END_TYPE:
                        return '}';
                    case Token::ARROW_TYPE:
                        return ' => ';
                    case Token::EOF_TYPE:
                        return '';
                }

                return '';
            }, $token_array)
        );
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
}