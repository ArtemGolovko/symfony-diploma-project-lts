<?php

namespace App\Service\Verification;

use App\Entity\User;
use App\Service\Mailer\Mailer;
use App\Service\Mailer\Receiver;
use App\Service\Verification\Exception\NewEmailAlreadyVerifiedException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use SymfonyCasts\Bundle\VerifyEmail\Exception\InvalidSignatureException;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class VerifyNewEmailService
{
    private VerifyEmailHelperInterface $verifyEmailHelper;

    private Mailer $mailer;

    private Security $security;

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
     * @param string $newEmail
     *
     * @return void
     */
    public function requestVerification(string $newEmail): void
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $signature = $this->verifyEmailHelper->generateSignature(
            'app_dashboard_verify_new_email', $user->getId(), $newEmail, ['new_email' => $newEmail]
        );

        $this->mailer->sendNewEmailVerification(new Receiver($user->getName(), $newEmail), $signature->getSignedUrl());
    }

    /**
     * @param Request $request
     *
     * @return void
     * @throws VerifyEmailExceptionInterface
     */
    public function verifyNewEmail(Request $request): void
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $newEmail = $request->get('new_email');

        if (null === $newEmail) {
            throw new InvalidSignatureException();
        }

        if ($user->getEmail() === $newEmail) {
            throw new NewEmailAlreadyVerifiedException();
        }

        $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $newEmail);

        $user->setEmail($newEmail);
        $this->entityManager->flush();
    }
}