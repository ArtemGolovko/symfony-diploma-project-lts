<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\ValueObject\Subscription;
use Doctrine\ORM\EntityManagerInterface;

class SubscriptionService
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function upgrade(User $user, $level): void
    {
        $subscription = $user->getSubscription();

        if ($subscription->isSubordinates($level))
            throw new \LogicException('Cannot downgrade subscription.');

        $subscription->setLevel($level, new \DateTimeImmutable('+1 week'));
        $this->em->flush();
    }

    public function expiresInDays(Subscription $subscription): ?int
    {
        if ($subscription->getLevel() === Subscription::HIERARCHY[0])
            return null;

        $expiresAt = $subscription->getExpiresAt();

        if (null === $expiresAt)
            return null;

        $diff = $expiresAt->diff(new \DateTimeImmutable());

        if (false === $diff)
            return null;

        return $diff->d + 1;
    }
}