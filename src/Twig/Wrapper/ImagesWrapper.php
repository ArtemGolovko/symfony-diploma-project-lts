<?php

namespace App\Twig\Wrapper;

class ImagesWrapper implements \Stringable
{
    /**
     * @var string[]
     */
    private array $images;

    /**
     * @var int
     */
    private int $index;

    /**
     * @param string[] $images
     */
    public function __construct(array $images)
    {
        $this->images = $images;
        $this->index = 0;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $image = $this->images[$this->index];
        $this->index += 1;

        if ($this->index >= count($this->images)) {
            $this->index = 0;
        }

        return $image;
    }
}