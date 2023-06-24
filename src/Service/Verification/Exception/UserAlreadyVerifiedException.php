<?php

namespace App\Service\Verification\Exception;

use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

final class UserAlreadyVerifiedException extends \Exception implements VerifyEmailExceptionInterface
{
    public function getReason(): string
    {
        return 'User has been already verified.';
    }
}