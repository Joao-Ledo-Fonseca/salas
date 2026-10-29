<?php

namespace Salas\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Salas\Core\Auth\AuthContext;

class AuthContextTest extends TestCase
{
    public function testIsAuthenticatedFalseWithoutSession(): void
    {
        $auth = new AuthContext();
        $this->assertFalse($auth->isAuthenticated());
    }

    public function testLevelMinusOneWithoutSession(): void
    {
        $auth = new AuthContext();
        $this->assertSame(-1, $auth->level());
    }

    public function testIdZeroWithoutSession(): void
    {
        $auth = new AuthContext();
        $this->assertSame(0, $auth->id());
    }

    public function testNameEmptyWithoutSession(): void
    {
        $auth = new AuthContext();
        $this->assertSame('', $auth->name());
    }

    public function testIdentityNullWithoutSession(): void
    {
        $auth = new AuthContext();
        $this->assertNull($auth->identity());
    }
}
