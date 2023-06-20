<?php

namespace App\Entity\ValueObject;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Subscription
{
    public const FREE = 'FREE';
    public const PLUS = 'PLUS';
    public const PRO = 'PRO';
    public const HIERARCHY = [self::FREE, self::PLUS, self::PRO];

    /**
     * @ORM\Column(type="string", length=4)
     */
    private string $level = self::HIERARCHY[0];

    /**
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $expiresAt = null;

    /**
     * @param string                  $level
     * @param \DateTimeImmutable|null $expiresAt
     */
    public function __construct(string $level = self::HIERARCHY[0], ?\DateTimeImmutable $expiresAt = null)
    {
        $this->setLevel($level, $expiresAt);
    }

    /**
     * @return string
     */
    public function getLevel(): string
    {
        if ($this->isExpired()) {
            return self::HIERARCHY[0];
        }

        return $this->level;
    }

    /**
     * @param string                  $level
     * @param \DateTimeImmutable|null $expiresAt
     *
     * @return Subscription
     */
    public function setLevel(string $level, ?\DateTimeImmutable $expiresAt = null): self
    {
        if (!in_array($level, self::HIERARCHY)) {
            throw new \InvalidArgumentException('Invalid level.');
        }

        if (self::HIERARCHY[0] !== $level && null === $expiresAt) {
            throw new \InvalidArgumentException("Cannot set level {$level} with unlimited lifetime");
        }

        $this->level = $level;
        $this->expiresAt = self::HIERARCHY[0] === $level ? null : $expiresAt;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        if (null === $this->expiresAt) {
            return false;
        }

        return (new \DateTimeImmutable()) > $this->expiresAt;
    }

    /**
     * @param string $level
     *
     * @return bool
     */
    public function isSubordinates(string $level): bool
    {
        if (!in_array($level, self::HIERARCHY)) {
            throw new \InvalidArgumentException('Invalid level.');
        }

        return array_search($level, self::HIERARCHY) <= array_search($this->getLevel(), self::HIERARCHY);
    }
}