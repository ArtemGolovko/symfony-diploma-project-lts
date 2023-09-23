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
     * @var int
     */
    private int $begin;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @var int|null
     */
    private ?int $end;

    /**
     * @param int      $begin
     * @param int|null $end
     *
     * @return void
     */
    public static function create(int $begin, ?int $end = null): Range
    {
        return (new Range())
            ->setBegin($begin)
            ->setEnd($end)
        ;
    }

    /**
     * @param $data
     *
     * @return Range
     */
    public static function from($data): Range
    {
        if (is_int($data)) {
            return Range::create($data);
        }

        if (is_array($data) && (count($data) >= 2) && is_int($data[0]) && is_int($data[1])) {
            return Range::create($data[0], $data[1]);
        }

        throw new \InvalidArgumentException("Invalid argument");
    }

    /**
     * @return int
     */
    public function getBegin(): int
    {
        return $this->begin;
    }

    /**
     * @param int $begin
     *
     * @return Range
     */
    public function setBegin(int $begin): Range
    {
        $this->begin = $begin;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getEnd(): ?int
    {
        return $this->end;
    }

    /**
     * @param int|null $end
     *
     * @return Range
     */
    public function setEnd(?int $end = null): Range
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Generates random number within range.
     *
     * @return int
     */
    public function generate(): int
    {
        if (!$this->end) {
            return $this->begin;
        }

        return mt_rand($this->begin, $this->end);
    }
}
