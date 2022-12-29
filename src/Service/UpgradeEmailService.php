<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UpgradeEmailService
{

    private RandomStringGenerator $generator;
    private EntityManagerInterface $em;

    public function __construct(RandomStringGenerator $generator, EntityManagerInterface $em)
    {
        $this->generator = $generator;
        $this->em = $em;
    }

    public function requestUpgrade(User $user, string $newEmail): string
    {
        $verificationCode = $this->generator->generate(255);

        $user
            ->setUpgradeEmail($newEmail)
            ->setUpgradeEmailVerificationCode($verificationCode)
        ;

        $this->em->flush();

        return $verificationCode;
    }

    public function upgradeEmail(User $user, string $verificationCode): bool
    {
        if ($user->getUpgradeEmailVerificationCode() !== $verificationCode) {
            return false;
        }

        $user
            ->setEmail($user->getUpgradeEmail())
            ->setUpgradeEmail(null)
            ->setUpgradeEmailVerificationCode(null)
        ;

        $this->em->flush();

        return true;
    }
}