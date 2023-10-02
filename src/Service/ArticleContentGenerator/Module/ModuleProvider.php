<?php

namespace App\Service\ArticleContentGenerator\Module;

class ModuleProvider implements ModuleProviderInterface
{
    /**
     * @var StaticModuleProvider
     */
    private StaticModuleProvider $staticModuleProvider;

    /**
     * @var UserModuleProvider
     */
    private UserModuleProvider $userModuleProvider;

    /**
     * @param StaticModuleProvider $staticModuleProvider
     * @param UserModuleProvider   $userModuleProvider
     */
    public function __construct(
        StaticModuleProvider $staticModuleProvider,
        UserModuleProvider $userModuleProvider
    ) {
        $this->staticModuleProvider = $staticModuleProvider;
        $this->userModuleProvider = $userModuleProvider;
    }

    /**
     * @inheritDoc
     */
    public function getModules(): array
    {
        return array_merge(
            $this->staticModuleProvider->getModules(),
            $this->userModuleProvider->getModules()
        );
    }
}
