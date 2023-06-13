<?php

namespace App\EventListener;

use App\Event\RegistrationSuccessEvent;
use App\Service\Mailer\Mailer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegistrationSuccessListener
{
    private Mailer $mailer;
    private UrlGeneratorInterface $urlGenerator;
    private VerifyEmailHelperInterface $verifyEmailHelper;

    public function __construct(
        VerifyEmailHelperInterface $verifyEmailHelper,
        Mailer $mailer,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->verifyEmailHelper = $verifyEmailHelper;
    }

    public function onRegistrationSuccess(RegistrationSuccessEvent $event)
    {
        $user = $event->getUser();
        $signature = $this->verifyEmailHelper->generateSignature(
            'app_verify_email',
            $user->getId(),
            $user->getEmail()
        );

        $this->mailer->sendEmailVerification($user, $signature->getSignedUrl());

        $session = $event->getRequest()->getSession();

        $session->set('redirect_path', $this->urlGenerator->generate('app_register'));
        $session->getFlashBag()->add('success', 'Для завершения регистрации подтвердите ваш email');
    }
}