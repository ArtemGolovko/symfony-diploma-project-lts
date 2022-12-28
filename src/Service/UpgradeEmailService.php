<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UpgradeEmailService
{

    private RandomStringGenerator $generator;
    private EntityManagerInterface $em;
    private Mailer $mailer;

    public function __construct(
        RandomStringGenerator $generator,
        EntityManagerInterface $em,
        Mailer $mailer
    ) {
        $this->generator = $generator;
        $this->em = $em;
        $this->mailer = $mailer;
    }

    public function requestUpgrade(User $user, string $newEmail)
    {
        $user
            ->setUpgradeEmail($newEmail)
            ->setUpgradeEmailVerificationCode($this->generator->generate(255))
        ;

        $this->mailer->sendUpgradeEmailVerification($user);

        $this->em->flush();
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