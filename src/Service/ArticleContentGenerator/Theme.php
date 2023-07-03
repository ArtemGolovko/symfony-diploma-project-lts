<?php

namespace App\Service\ArticleContentGenerator;

use Symfony\Component\Validator\Constraints as Assert;

class Theme
{
    /**
     * @var string
     */
    public string $name;

    /**
     * @var string[]
     * @Assert\All(constraints={
     *  @Assert\NotBlank
     *  @Assert\Length(max=255)
     * })
     */
    public array $titles;

    /**
     * @var array[]
     */
    public array $paragraphs;

    /**
     * @var string[]
     */
    public array $images;
}