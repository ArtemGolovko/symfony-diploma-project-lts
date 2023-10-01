<?php

namespace App\Entity\Dto;

class PromotedWord implements \Serializable, \Stringable
{
    /**
     * @var string
     */
    private string $word;

    /**
     * @var int
     */
    private int $repetitions;

    /**
     * @param string $word
     * @param int    $repetitions
     *
     * @return PromotedWord
     */
    public static function create(string $word, int $repetitions): PromotedWord
    {
        return (new PromotedWord())
            ->setWord($word)
            ->setRepetitions($repetitions)
        ;
    }

    /**
     * @return string
     */
    public function getWord(): string
    {
        return $this->word;
    }

    /**
     * @param string $word
     *
     * @return PromotedWord
     */
    public function setWord(string $word): self
    {
        $this->word = $word;

        return $this;
    }

    /**
     * @return int
     */
    public function getRepetitions(): int
    {
        return $this->repetitions;
    }

    /**
     * @param int $repetitions
     *
     * @return PromotedWord
     */
    public function setRepetitions(int $repetitions): self
    {
        $this->repetitions = $repetitions;

        return $this;
    }

    /**
     * @return string
     */
    public function serialize(): string
    {
        return $this->repetitions . ":" . $this->word;
    }

    /**
     * @param string $data
     *
     * @return void
     */
    public function unserialize($data): void
    {
        $this->repetitions = 0;
        $this->word = '';
        sscanf($data, "%d:%s", $this->repetitions, $this->word);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->word;
    }
}
