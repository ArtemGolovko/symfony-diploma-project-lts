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
        if (!$this->forms) {
            if (1 > count($this->keywords)) {
                throw new \OutOfBoundsException();
            }

            return $this->keywords[0];
        }

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
