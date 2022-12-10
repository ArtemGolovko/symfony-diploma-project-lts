<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Mailer
{

    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmailVerification(User $user, string $verificationCode)
    {
        $this->send(
            'email/email_verification.html.twig',
            'Подтверждение електронной почты',
            $user,
            function (TemplatedEmail $email) use ($verificationCode) {
                $email->context([
                    'verification_code' => $verificationCode
                ]);
            }
        );
    }

    private function send(string $template, string $subject, User $user, \Closure $callback = null)
    {
        $email = (new TemplatedEmail())
            ->from(new Address('noreply@blablaarticle.com', 'BlaBlaArticle'))
            ->to(new Address($user->getEmail(), $user->getName()))
            ->subject($subject)
            ->htmlTemplate($template)
        ;

        if ($callback) {
            $callback($email);
        }

        $this->mailer->send($email);
    }
}