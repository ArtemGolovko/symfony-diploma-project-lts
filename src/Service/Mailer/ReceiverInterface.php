<?php

namespace App\Service\Mailer;

interface ReceiverInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getEmail(): string;
}