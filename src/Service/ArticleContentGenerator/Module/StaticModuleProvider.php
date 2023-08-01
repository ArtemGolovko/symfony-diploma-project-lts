<?php

namespace App\Service\ArticleContentGenerator\Module;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

class StaticModuleProvider implements ModuleProviderInterface
{
    /**
     * @var StaticModule[]
     */
    private array $modules = [];

    /**
     * @param string          $resourcesDir
     * @param string          $modulesDir
     * @param KernelInterface $kernel
     */
    public function __construct(string $resourcesDir, string $modulesDir, KernelInterface $kernel)
    {
        $finder = new Finder();

        $projectDir = $kernel->getProjectDir();
        $finder->in($projectDir . '/' . $resourcesDir . $modulesDir);

        foreach ($finder as $file) {
            $this->modules[] = new StaticModule($file->getContents());
        }
    }

    /**
     * @inheritDoc
     */
    public function getModules(): array
    {
        return $this->modules;
    }
}