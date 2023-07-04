<?php

namespace App\EventListener;

use App\Event\RegistrationSuccessEvent;
use App\Service\RedirectService;
use App\Service\Verification\VerifyEmailService;

class RegistrationSuccessListener
{
    /**
     * @var VerifyEmailService
     */
    private VerifyEmailService $verifyEmail;

    /**
     * @var RedirectService
     */
    private RedirectService $redirectService;

    /**
     * @param VerifyEmailService $verifyEmail
     * @param RedirectService    $redirectService
     */
    public function __construct(
        VerifyEmailService $verifyEmail,
        RedirectService $redirectService
    ) {
        $this->verifyEmail = $verifyEmail;
        $this->redirectService = $redirectService;
    }

    /**
     * @param RegistrationSuccessEvent $event
     *
     * @return void
     */
    public function onRegistrationSuccess(RegistrationSuccessEvent $event): void
    {
        $user = $event->getUser();
        $this->verifyEmail->requestVerification($user);

        $session = $event->getRequest()->getSession();

        $this->redirectService->setRedirectPathToRoute('app_register');
        $session->getFlashBag()->add('success', 'Для завершения регистрации подтвердите ваш email');
    }
}