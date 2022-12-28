<?php

namespace App\Service\Mailer;

interface ReceiverInterface
{
    public function getName(): string;

    public function getEmail(): string;
}