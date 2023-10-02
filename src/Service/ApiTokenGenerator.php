<?php

namespace App\Service;

class ApiTokenGenerator
{
    /**
     * @return string
     */
    public function generate(): string
    {
        return base64_encode(random_bytes(24));
    }
}
