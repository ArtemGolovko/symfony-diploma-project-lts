<?php

namespace App\Service\Verification\Exception;

use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

final class NewEmailAlreadyVerifiedException extends \Exception implements VerifyEmailExceptionInterface
{
    /**
     * @return string
     */
    public function getReason(): string
    {
        return 'New email has been already verified.';
    }
}