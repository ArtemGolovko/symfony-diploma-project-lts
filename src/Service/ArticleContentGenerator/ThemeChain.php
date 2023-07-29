<?php

namespace App\Service\ArticleContentGenerator;

class ThemeChain
{
    /**
     * @var ThemeProviderInterface[]
     */
    private array $providers;

    public function __construct()
    {
        $this->providers = [];
    }

    /**
     * @param ThemeProviderInterface $provider
     *
     * @return void
     */
    public function addProvider(ThemeProviderInterface $provider): void
    {
        $this->providers[] = $provider;
    }

    /**
     * @return Theme[]
     */
    public function getThemes(): array
    {
        return array_merge(
            ...array_map(function (ThemeProviderInterface $provider) {
                return $provider->getThemes();
            }, $this->providers)
        );
    }

    /**
     * @return string[]
     */
    public function getThemeNames(): array
    {
        return array_merge(
            ...array_map(function (ThemeProviderInterface $provider) {
                return $provider->getThemeNames();
            }, $this->providers)
        );
    }
}