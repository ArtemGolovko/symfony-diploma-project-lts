<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class VerifyEmailService
{


    private RandomStringGenerator $generator;
    private EntityManagerInterface $em;

    public function __construct(RandomStringGenerator $generator, EntityManagerInterface $em)
    {
        $this->generator = $generator;
        $this->em = $em;
    }

    public function requestVerification(User $user): string
    {
        $verificationCode = $this->generator->generate(255);

        $user->setVerificationCode($verificationCode);

        $this->em->flush();

        return $verificationCode;
    }

    public function verifyEmail(User $user, string $verificationCode): bool
    {
        if ($user->getVerificationCode() !== $verificationCode) {
            return false;
        }

        $user->setVerificationCode(null);

        $this->em->flush();

        return true;
    }
}