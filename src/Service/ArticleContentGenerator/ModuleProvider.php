<?php

namespace App\Service\ArticleContentGenerator;

class ModuleProvider implements ModuleProviderInterface
{
    /**
     * @var StaticModuleProvider
     */
    private StaticModuleProvider $staticModuleProvider;

    /**
     * @var DoctrineModuleProvider
     */
    private DoctrineModuleProvider $doctrineModuleProvider;

    /**
     * @param StaticModuleProvider $staticModuleProvider
     * @param DoctrineModuleProvider $doctrineModuleProvider
     */
    public function __construct(
        StaticModuleProvider $staticModuleProvider,
        DoctrineModuleProvider $doctrineModuleProvider
    ) {
        $this->staticModuleProvider = $staticModuleProvider;
        $this->doctrineModuleProvider = $doctrineModuleProvider;
    }

    /**
     * @inheritDoc
     */
    public function getModules(): array
    {
        return array_merge(
            $this->staticModuleProvider->getModules(),
            $this->doctrineModuleProvider->getModules()
        );
    }
}