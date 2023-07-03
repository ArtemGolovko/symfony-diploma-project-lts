<?php

namespace App\Entity\Dto;

class PromotedWord implements \Serializable
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
     */
    public function __construct(string $word, int $repetitions)
    {
        $this->word = $word;
        $this->repetitions = $repetitions;
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
    public function serialize()
    {
        return $this->repetitions . ":" . $this->word;
    }

    /**
     * @param $data
     *
     * @return void
     */
    public function unserialize($data)
    {
        sscanf($data, "%d:%s", $this->repetitions, $this->word);
    }
}