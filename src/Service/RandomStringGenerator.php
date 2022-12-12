<?php

namespace App\Service;

class RandomStringGenerator
{
    public function generate(int $length): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[rand(0, 61)];
        }

        return $randomString;
    }
}