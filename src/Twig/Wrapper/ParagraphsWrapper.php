<?php

namespace App\Twig\Wrapper;

class ParagraphsWrapper implements \Stringable
{
    /**
     * @var string[][]
     */
    private array $paragraphs;

    /**
     * @var int
     */
    private int $index;

    /**
     * @param string[][] $paragraphs
     */
    public function __construct(array $paragraphs)
    {
        $this->paragraphs = $paragraphs;
        $this->index = 0;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $paragraphs = $this->paragraphs[$this->index];
        $this->index += 1;

        if (count($paragraphs) === 1) {
            return $paragraphs[0];
        }

        return implode(
            ' ',
            array_map(function (string $paragraph) {
                return "<p>$paragraph</p>";
            }, $paragraphs)
        );
    }
}