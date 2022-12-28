<?php

namespace App\Service\Mailer;

interface SenderInterface
{
    public function getName(): string;

    public function getEmail(): string;
}