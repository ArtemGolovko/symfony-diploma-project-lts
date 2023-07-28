<?php

namespace App\Service\ArticleContentGenerator;

interface ModuleProviderInterface
{
    /**
     * @return ModuleInterface[]
     */
    public function getModules(): array;
}