<?php

namespace App\Service\Verification;

use App\Entity\User;
use App\Service\Mailer\Mailer;
use App\Service\Verification\Exception\UserAlreadyVerifiedException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Security\Core\Security;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class VerifyEmail
{
    /**
     * @var VerifyEmailHelperInterface
     */
    private VerifyEmailHelperInterface $verifyEmailHelper;

    /**
     * @var Mailer
     */
    private Mailer $mailer;

    /**
     * @var Security
     */
    private Security $security;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param VerifyEmailHelperInterface $verifyEmailHelper
     * @param Mailer                     $mailer
     * @param Security                   $security
     * @param EntityManagerInterface     $entityManager
     */
    public function __construct(
        VerifyEmailHelperInterface $verifyEmailHelper,
        Mailer $mailer,
        Security $security,
        EntityManagerInterface $entityManager
    ) {
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->mailer = $mailer;
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    /**
     * @param User|null $user
     *
     * @return void
     * @throws TransportExceptionInterface
     */
    public function requestVerification(?User $user = null): void
    {
        if (null === $user) {
            $user = $this->security->getUser();
        }

        if ($user->isVerified()) {
            return;
        }

        $signature = $this->verifyEmailHelper->generateSignature(
            'app_verify_email',
            $user->getId(),
            $user->getEmail()
        );

        $this->mailer->sendEmailVerification($user, $signature->getSignedUrl());
    }

    /**
     * @param Request $request
     *
     * @return void
     * @throws VerifyEmailExceptionInterface
     */
    public function verifyEmail(Request $request): void
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if ($user->isVerified()) {
            throw new UserAlreadyVerifiedException();
        }

        $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());

        $user->setIsVerified(true);
        $this->entityManager->flush();
    }
}
