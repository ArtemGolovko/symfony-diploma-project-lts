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

    public function sendEmailVerification(ReceiverInterface $sender, string $signedUrl)
    {
        $this->send(
            'email/email_verification.html.twig',
            'Подтверждение электронной почты',
            $sender,
            function (TemplatedEmail $email) use ($signedUrl) {
                $email->context([
                    'signed_url' => $signedUrl
                ]);
            }
        );
    }

    public function sendNewEmailVerification(ReceiverInterface $sender, $signedUrl)
    {
        $this->send(
            'email/new_email_verification.html.twig',
            'Изменение электронной почты',
            $sender,
            function (TemplatedEmail $email) use ($signedUrl) {
                $email->context([
                    'signed_url' => $signedUrl
                ]);
            }
        );
    }

    private function send(string $template, string $subject, ReceiverInterface $sender, \Closure $callback = null)
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