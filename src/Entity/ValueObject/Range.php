<?php

namespace App\Entity\ValueObject;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Range
{
    /**
     * @ORM\Column(type="smallint")
     */
    private int $begin;

    /**
     * @ORM\Column(type="smallint")
     */
    private int $end;

    /**
     * @param int      $begin
     * @param int|null $end
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(int $begin, ?int $end = null)
    {
        if ($begin > $end) {
            throw new \InvalidArgumentException('Beginning of range cannot be bigger ending of range.');
        }

        $this->begin = $begin;
        $this->end = $end ?? $begin;
    }

    /**
     * @return int
     */
    public function getBegin(): int
    {
        return $this->begin;
    }

    /**
     * @return int
     */
    public function getEnd(): int
    {
        return $this->end;
    }

    /**
     * @param int      $begin
     * @param int|null $end
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    public function set(int $begin, ?int $end = null): void
    {
        if ($begin > $end) {
            throw new \InvalidArgumentException('Beginning of range cannot be bigger ending of range.');
        }

        $this->begin = $begin;
        $this->end = $end ?? $begin;
    }

    /**
     * Generates random number within range.
     *
     * @return int
     */
    public function generate(): int
    {
        return mt_rand($this->begin, $this->end);
    }
}