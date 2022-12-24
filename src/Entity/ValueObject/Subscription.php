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

    /**
     * @ORM\Column(type="string", length=4)
     */
    private string $level = self::FREE;

    /**
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $expiresAt = null;

    /**
     * @param string $level
     * @param \DateTimeImmutable|null $expiresAt
     */
    public function __construct(string $level = self::FREE, ?\DateTimeImmutable $expiresAt = null)
    {
        $this->setLevel($level, $expiresAt);
    }


    /**
     * @return string
     */
    public function getLevel(): string
    {
        if ($this->isExpired()) return self::FREE;
        
        return $this->level;
    }

    /**
     * @param string $level
     * @param \DateTimeImmutable|null $expiresAt
     */
    public function setLevel(string $level, ?\DateTimeImmutable $expiresAt = null): self
    {
        if (!in_array($level, [self::FREE, self::PLUS, self::PRO]))
            throw new \InvalidArgumentException('Invalid level.');

        if (self::FREE !== $level && null === $expiresAt)
                throw new \InvalidArgumentException("Cannot set level {$level} with unlimited lifetime");

        $this->level = $level;
        $this->expiresAt = self::FREE === $level ? null : $expiresAt;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function isExpired(): bool
    {
        if (null === $this->expiresAt) return false;

        return (new \DateTimeImmutable()) > $this->expiresAt;
    }

    public function isSubordinates(string $level): bool
    {
        if (!in_array($level, [self::FREE, self::PLUS, self::PRO]))
            throw new \InvalidArgumentException('Invalid level.');

        $currentLevel = $this->getActualLevel();

        if (self::FREE === $level || self::PRO === $currentLevel || $level === $currentLevel)
            return true;

        return false;
    }
}