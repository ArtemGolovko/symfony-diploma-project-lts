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

    public function __construct(
        EntityManagerInterface $em,
        RandomStringGenerator $generator,
        Mailer $mailer
    ) {
        $this->em = $em;
        $this->generator = $generator;
        $this->mailer = $mailer;
    }

    public function onRegistrationSuccess(RegistrationSuccessEvent $event)
    {
        $user = ($event->getUser())
            ->setVerificationCode($this->generator->generate(255))
        ;

        $this->em->flush();

        $this->mailer->sendEmailVerification($user);
    }
}