<?php

namespace App\Security;

class TokenGenerator
{
    public function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}