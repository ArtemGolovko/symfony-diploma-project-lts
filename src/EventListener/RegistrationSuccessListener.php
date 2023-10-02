<?php

namespace App\EventListener;

use App\Event\RegistrationSuccessEvent;
use App\Service\Redirect;
use App\Service\Verification\VerifyEmail;

class RegistrationSuccessListener
{
    /**
     * @var VerifyEmail
     */
    private VerifyEmail $verifyEmail;

    /**
     * @var Redirect
     */
    private Redirect $redirect;

    /**
     * @param VerifyEmail $verifyEmail
     * @param Redirect    $redirect
     */
    public function __construct(
        VerifyEmail $verifyEmail,
        Redirect $redirect
    ) {
        $this->verifyEmail = $verifyEmail;
        $this->redirect = $redirect;
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

        $this->redirect->setRedirectPathToRoute('app_register');
        $session->getFlashBag()->add('success', 'Для завершения регистрации подтвердите ваш email');
    }
}
