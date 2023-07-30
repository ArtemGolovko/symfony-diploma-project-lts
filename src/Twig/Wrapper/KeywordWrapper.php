<?php

namespace App\Twig\Wrapper;

class KeywordWrapper implements \Stringable
{
    /**
     * @var string[]
     */
    private array $keywords;

    /**
     * @param string[] $keywords
     */
    public function __construct(array $keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * @param int $form
     *
     * @return string
     */
    public function getForm(int $form): string
    {
        if ($form >= count($this->keywords)) {
            throw new \OutOfBoundsException();
        }

        return $this->keywords[$form];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->keywords[0];
    }
}