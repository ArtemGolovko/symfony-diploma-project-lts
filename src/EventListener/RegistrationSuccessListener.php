<?php

namespace App\EventListener;

use App\Event\RegistrationSuccessEvent;
use App\Service\Mailer\Mailer;
use App\Service\VerifyEmailService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationSuccessListener
{
    private Mailer $mailer;
    private UrlGeneratorInterface $urlGenerator;
    private VerifyEmailService $verifyEmail;

    public function __construct(
        VerifyEmailService $verifyEmail,
        Mailer $mailer,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->verifyEmail = $verifyEmail;
    }

    public function onRegistrationSuccess(RegistrationSuccessEvent $event)
    {
        $user = $event->getUser();
        $verificationCode = $this->verifyEmail->requestVerification($user);

        $this->mailer->sendEmailVerification($user, $verificationCode);

        $session = $event->getRequest()->getSession();

        $session->set('redirect_path', $this->urlGenerator->generate('app_register'));
        $session->getFlashBag()->add('success', 'Для завершения регистрации подтвердите ваш email');
    }
}