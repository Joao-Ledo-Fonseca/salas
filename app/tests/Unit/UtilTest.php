<?php

namespace Salas\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Salas\Core\Support\DateUtil;
use Salas\Core\Support\ImageUtil;

class UtilTest extends TestCase
{
    public function testParseDataPtValid(): void
    {
        $date = DateUtil::parseDataPt('29/10/2026');
        $this->assertInstanceOf(\DateTime::class, $date);
        $this->assertSame('29/10/2026', $date->format('d/m/Y'));
    }

    public function testParseDataPtInvalid(): void
    {
        $this->assertNull(DateUtil::parseDataPt(''));
        $this->assertNull(DateUtil::parseDataPt('not-a-date'));
    }

    public function testParseDataPtWithWeekday(): void
    {
        $date = DateUtil::parseDataPt('Qua 29/10/2026');
        $this->assertInstanceOf(\DateTime::class, $date);
    }

    public function testTraduzDataPtToEn(): void
    {
        $this->assertSame('Oct', DateUtil::traduz_data('Out', 'en'));
    }

    public function testTraduzDataEnToPt(): void
    {
        $this->assertSame('Out', DateUtil::traduz_data('Oct', 'pt'));
    }

    public function testImagemUploadEmpty(): void
    {
        $this->assertFalse(ImageUtil::imagemUpload(''));
    }

    public function testImagemUploadNonExistent(): void
    {
        $this->assertFalse(ImageUtil::imagemUpload('/tmp/nao_existe_xyz.jpg'));
    }
}
