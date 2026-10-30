<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Salas\Core\Validation\Validator;

class ValidatorTest extends TestCase
{
    public function testInt(): void
    {
        $this->assertSame(5, Validator::int('5'));
        $this->assertSame(0, Validator::int('abc'));
        $this->assertSame(0, Validator::int(null));
    }

    public function testString(): void
    {
        $this->assertSame('abc', Validator::string('abc'));
        $this->assertSame('', Validator::string(null));
    }
}
?>
