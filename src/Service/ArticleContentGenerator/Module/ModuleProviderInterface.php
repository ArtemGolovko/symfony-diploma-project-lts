<?php

namespace App\Service\ArticleContentGenerator\Module;

interface ModuleProviderInterface
{
    /**
     * @return ModuleInterface[]
     */
    public function getModules(): array;
}