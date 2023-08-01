<?php

namespace App\Service\ArticleContentGenerator\Theme;

use Symfony\Component\Validator\Constraints as Assert;

class Theme
{
    /**
     * @var string
     */
    public string $name;

    /**
     * @Assert\All(constraints={
     *  @Assert\NotBlank
     *  @Assert\Length(max=255)
     * })
     * @var string[]
     */
    public array $titles;

    /**
     * @var string[]
     */
    public array $paragraphs;

    /**
     * @var string[]
     */
    public array $images;
}