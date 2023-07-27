<?php

namespace App\Service\ArticleContentGenerator;

interface ModuleInterface
{
    /**
     * @return string
     */
    public function getTemplate(): string;
}