<?php
declare(strict_types=1);

namespace Salas\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Salas\Core\Theme\ThemeFontFamily;

final class ThemeFontFamilyTest extends TestCase
{
    public function testOptionsExposePrimaryNamesAndKeepFallbacksInStacks(): void
    {
        $options = ThemeFontFamily::options();

        $this->assertArrayHasKey('Arial', $options);
        $this->assertSame('Arial, Helvetica, sans-serif', $options['Arial']);
        $this->assertSame("Georgia, 'Times New Roman', serif", $options['Georgia']);
        $this->assertSame("'Inter', sans-serif", $options['Inter']);
        $this->assertCount(20, ThemeFontFamily::googleOptions());
        $this->assertArrayNotHasKey('Inter', ThemeFontFamily::safeOptions());
        $this->assertArrayNotHasKey('Arial', ThemeFontFamily::googleOptions());
    }

    public function testSelectionCanBeReadFromAnExistingFontStack(): void
    {
        $this->assertSame(
            'Georgia',
            ThemeFontFamily::selectionFromStack("Georgia, 'Times New Roman', serif")
        );
    }

    public function testUnsupportedSelectionsAreRejected(): void
    {
        $this->assertNull(ThemeFontFamily::stackForSelection('Arial, sans-serif'));
        $this->assertNull(ThemeFontFamily::stackForSelection('url(evil)'));
    }

    public function testGoogleFontsUrlsAreGeneratedOnlyForApprovedGoogleFonts(): void
    {
        $url = ThemeFontFamily::googleFontsUrlForSelection('Open Sans');

        $this->assertSame(
            'https://fonts.googleapis.com/css2?family=Open%20Sans%3Awght%40300%3B400%3B500%3B600%3B700&display=swap',
            $url
        );
        $this->assertNull(ThemeFontFamily::googleFontsUrlForSelection('Arial'));
        $this->assertSame(
            $url,
            ThemeFontFamily::googleFontsUrlForStack("'Open Sans', sans-serif")
        );
        $this->assertNull(ThemeFontFamily::googleFontsUrlForStack('Arial, Helvetica, sans-serif'));
    }
}
