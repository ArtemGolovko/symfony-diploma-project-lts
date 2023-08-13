<?php

namespace App\Twig\Wrapper;

class KeywordWrapper implements \Stringable
{
    /**
     * @var string[]
     */
    private array $keywords;

    /**
     * @var bool
     */
    private bool $forms;

    /**
     * @param string[] $keywords
     */
    public function __construct(array $keywords, bool $forms)
    {
        $this->keywords = $keywords;
        $this->forms = $forms;
    }

    /**
     * @param int $form
     *
     * @return string
     */
    public function getForm(int $form): string
    {
        if (!$this->forms || $form >= count($this->keywords)) {
            return $this->keywords[0];
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
