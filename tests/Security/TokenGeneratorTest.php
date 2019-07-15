<?php

namespace App\Tests\Security;

use PHPUnit\Framework\TestCase;
use App\Security\TokenGenerator;

class TokenGeneratorTest extends TestCase
{
    public function testTokenGeneration()
    {
        $tokenGen = new TokenGenerator();
        $token = $tokenGen->generateToken();
        $this->assertFalse($token == '');
        $this->assertLessThanOrEqual(64, strlen($token));
    }
}