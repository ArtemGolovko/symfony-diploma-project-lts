<?php

namespace App\Service\Mailer;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Mailer
{
    private MailerInterface $mailer;

    /**
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param ReceiverInterface $sender
     * @param string            $signedUrl
     *
     * @return void
     * @throws TransportExceptionInterface
     */
    public function sendEmailVerification(ReceiverInterface $sender, string $signedUrl): void
    {
        $this->send(
            'email/email_verification.html.twig',
            'Подтверждение электронной почты',
            $sender,
            function (TemplatedEmail $email) use ($signedUrl) {
                $email->context([
                    'signed_url' => $signedUrl,
                ]);
            }
        );
    }

    /**
     * @param ReceiverInterface $sender
     * @param string            $signedUrl
     *
     * @return void
     * @throws TransportExceptionInterface
     */
    public function sendNewEmailVerification(ReceiverInterface $sender, string $signedUrl): void
    {
        $this->send(
            'email/new_email_verification.html.twig',
            'Изменение электронной почты',
            $sender,
            function (TemplatedEmail $email) use ($signedUrl) {
                $email->context([
                    'signed_url' => $signedUrl,
                ]);
            }
        );
    }

    /**
     * @param string $template
     * @param string $subject
     * @param ReceiverInterface $sender
     * @param \Closure|null $callback
     *
     * @return void
     * @throws TransportExceptionInterface
     */
    private function send(string $template, string $subject, ReceiverInterface $sender, \Closure $callback = null): void
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