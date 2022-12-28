<?php

namespace App\Service\Mailer;

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

    public function sendEmailVerification(SenderInterface $sender, string $verificationCode)
    {
        $this->send(
            'email/email_verification.html.twig',
            'Подтверждение электронной почты',
            $sender,
            function (TemplatedEmail $email) use ($verificationCode) {
                $email->context([
                    'verification_code' => $verificationCode
                ]);
            }
        );
    }

    public function sendUpgradeEmailVerification(SenderInterface $sender, $verificationCode)
    {
        $this->send(
            'email/upgrade_email_verification.html.twig',
            'Изменение электронной почты',
            $sender,
            function (TemplatedEmail $email) use ($verificationCode) {
                $email->context([
                    'verification_code' => $verificationCode
                ]);
            }
        );
    }

    private function send(string $template, string $subject, SenderInterface $sender, \Closure $callback = null)
    {
        $email = (new TemplatedEmail())
            ->from(new Address('noreply@blablaarticle.com', 'BlaBlaArticle'))
            ->to(new Address($sender->getEmail(), $sender->getName()))
            ->subject($subject)
            ->htmlTemplate($template)
        ;

        if ($callback) {
            $callback($email);
        }

        $this->mailer->send($email);
    }
}