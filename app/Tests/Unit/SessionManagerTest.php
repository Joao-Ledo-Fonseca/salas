<?php

namespace Salas\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Salas\Core\Auth\SessionManager;

class SessionManagerTest extends TestCase
{
    public function testStartDoesNotFail(): void
    {
        $sm = new SessionManager();
        $sm->start();
        $this->assertSame(PHP_SESSION_ACTIVE, session_status());
    }

    public function testIdentityNullWithoutLogin(): void
    {
        $sm = new SessionManager();
        $sm->start();
        $this->assertNull($sm->identity());
    }

    public function testLastActivityNullWithoutLogin(): void
    {
        $sm = new SessionManager();
        $sm->start();
        $this->assertNull($sm->lastActivity());
    }
}
