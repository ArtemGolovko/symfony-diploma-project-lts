<?php

namespace App\Service\ArticleContentGenerator;

class ModuleProvider implements ModuleProviderInterface
{
    /**
     * @var StaticModuleProvider
     */
    private StaticModuleProvider $staticModuleProvider;

    /**
     * @var UserModuleProvider
     */
    private UserModuleProvider $doctrineModuleProvider;

    /**
     * @param StaticModuleProvider $staticModuleProvider
     * @param UserModuleProvider   $doctrineModuleProvider
     */
    public function __construct(
        StaticModuleProvider $staticModuleProvider,
        UserModuleProvider $doctrineModuleProvider
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