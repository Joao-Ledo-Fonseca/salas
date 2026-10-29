<?php

namespace Salas\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Salas\Core\Support\Util;

class UtilTest extends TestCase
{
    public function testParseDataPtValid(): void
    {
        $date = Util::parseDataPt('29/10/2026');
        $this->assertInstanceOf(\DateTime::class, $date);
        $this->assertSame('29/10/2026', $date->format('d/m/Y'));
    }

    public function testParseDataPtInvalid(): void
    {
        $this->assertNull(Util::parseDataPt(''));
        $this->assertNull(Util::parseDataPt('not-a-date'));
    }

    public function testParseDataPtWithWeekday(): void
    {
        $date = Util::parseDataPt('Qua 29/10/2026');
        $this->assertInstanceOf(\DateTime::class, $date);
    }

    public function testTraduzDataPtToEn(): void
    {
        $this->assertSame('Oct', Util::traduz_data('Out', 'en'));
    }

    public function testTraduzDataEnToPt(): void
    {
        $this->assertSame('Out', Util::traduz_data('Oct', 'pt'));
    }

    public function testImagemUploadEmpty(): void
    {
        $this->assertFalse(Util::imagemUpload(''));
    }

    public function testImagemUploadNonExistent(): void
    {
        $this->assertFalse(Util::imagemUpload('/tmp/nao_existe_xyz.jpg'));
    }
}
