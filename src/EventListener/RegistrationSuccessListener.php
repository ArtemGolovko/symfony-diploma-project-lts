<?php

namespace App\EventListener;

use App\Event\RegistrationSuccessEvent;
use App\Service\Verification\VerifyEmailService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationSuccessListener
{
    private UrlGeneratorInterface $urlGenerator;
    private VerifyEmailService $verifyEmail;

    public function __construct(
        VerifyEmailService $verifyEmail,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->verifyEmail = $verifyEmail;
    }

    public function onRegistrationSuccess(RegistrationSuccessEvent $event)
    {
        $user = $event->getUser();
        $this->verifyEmail->requestVerification($user);

        $session = $event->getRequest()->getSession();

        $session->set('redirect_path', $this->urlGenerator->generate('app_register'));
        $session->getFlashBag()->add('success', 'Для завершения регистрации подтвердите ваш email');
    }
}