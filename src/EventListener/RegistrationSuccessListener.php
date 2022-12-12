<?php

namespace App\EventListener;

use App\Event\RegistrationSuccessEvent;
use App\Service\Mailer;
use App\Service\RandomStringGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationSuccessListener
{
    private EntityManagerInterface $em;
    private RandomStringGenerator $generator;
    private Mailer $mailer;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        EntityManagerInterface $em,
        RandomStringGenerator $generator,
        Mailer $mailer,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->em = $em;
        $this->generator = $generator;
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
    }

    public function onRegistrationSuccess(RegistrationSuccessEvent $event)
    {
        $user = ($event->getUser())
            ->setVerificationCode($this->generator->generate(255))
        ;

        $this->em->flush();

        $this->mailer->sendEmailVerification($user);

        $request = $event->getRequest();
        $session = $request->getSession();

        $session->set('redirect_path', $this->urlGenerator->generate('app_register'));
        $session->getFlashBag()->add('success', 'Для завершения регистрации подтвердите ваш email');
    }
}