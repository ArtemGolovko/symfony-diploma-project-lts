<?php

namespace App\Service\ArticleContentGenerator;

class StaticModule implements ModuleInterface
{
    /**
     * @var string
     */
    private string $template;

    /**
     * @param string $template
     */
    public function __construct(string $template)
    {
        $this->template = $template;
    }

    /**
     * @inheritDoc
     */
    public function getTemplate(): string
    {
        return $this->template;
    }
}