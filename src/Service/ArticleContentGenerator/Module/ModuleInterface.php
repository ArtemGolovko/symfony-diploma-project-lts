<?php

namespace App\Service\ArticleContentGenerator\Module;

interface ModuleInterface
{
    /**
     * @return string
     */
    public function getTemplate(): string;
}