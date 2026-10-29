<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Salas\Infrastructure\Support\Csrf;

class CsrfTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];
@session_start();
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public function testTokenGerado(): void
    {
        $t = Csrf::token();
        $this->assertNotEmpty($t);
        $this->assertSame($t, Csrf::token());
    }

    public function testValidacaoCorreta(): void
    {
        $t = Csrf::token();
        $this->assertTrue(Csrf::validate($t));
    }

    public function testValidacaoIncorreta(): void
    {
        Csrf::token();
        $this->assertFalse(Csrf::validate('token-invalido'));
        $this->assertFalse(Csrf::validate(null));
        $this->assertFalse(Csrf::validate(''));
    }
}
?>
