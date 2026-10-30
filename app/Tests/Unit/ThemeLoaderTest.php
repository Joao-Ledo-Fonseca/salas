<?php
declare(strict_types=1);

namespace Salas\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Salas\Core\Theme\Theme;
use Salas\Core\Theme\ThemeLoader;

class ThemeLoaderTest extends TestCase
{
    private string $tmpDir = '';

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'salas-theme-test-' . uniqid('', true);
        mkdir($this->tmpDir, 0777, true);
    }

    protected function tearDown(): void
    {
        putenv('APP_THEME');
        $this->removeDir($this->tmpDir);
    }

    private function removeDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = array_diff(scandir($dir) ?: [], ['.', '..']);
        foreach ($items as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path) && !is_link($path)) {
                $this->removeDir($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }

    private function writeManifest(array $manifest): void
    {
        file_put_contents(
            $this->tmpDir . DIRECTORY_SEPARATOR . 'themes.json',
            json_encode($manifest, JSON_THROW_ON_ERROR)
        );
    }

    private function writeTheme(string $key, array $data): void
    {
        file_put_contents(
            $this->tmpDir . DIRECTORY_SEPARATOR . $key . '.json',
            json_encode($data, JSON_THROW_ON_ERROR)
        );
    }

    private function validThemeData(): array
    {
        return [
            'name' => 'Teste',
            'fontFamily' => 'Arial, sans-serif',
            'fontSizeBase' => '14px',
            'colors' => ['bg' => '#ffffff', 'text' => '#000000'],
            'dashboard' => ['disponivel' => '#ffffff'],
        ];
    }

    public function testLoadValidTheme(): void
    {
        $this->writeManifest(['default' => 'light', 'available' => ['light']]);
        $this->writeTheme('light', $this->validThemeData());

        $theme = ThemeLoader::load($this->tmpDir, 'light');

        $this->assertSame('light', $theme->key);
        $this->assertSame('Teste', $theme->name);
        $this->assertArrayHasKey('bg', $theme->colors);
        $this->assertArrayHasKey('disponivel', $theme->dashboard);
    }

    public function testInvalidManifestJsonThrows(): void
    {
        file_put_contents($this->tmpDir . DIRECTORY_SEPARATOR . 'themes.json', '{invalid json');

        $this->expectException(\RuntimeException::class);
        ThemeLoader::load($this->tmpDir);
    }

    public function testManifestMissingFieldsThrows(): void
    {
        $this->writeManifest(['default' => 'light']);

        $this->expectException(\RuntimeException::class);
        ThemeLoader::load($this->tmpDir);
    }

    public function testRequestedThemeNotAvailableFallsBackToDefault(): void
    {
        $this->writeManifest(['default' => 'light', 'available' => ['light']]);
        $this->writeTheme('light', $this->validThemeData());

        $theme = ThemeLoader::load($this->tmpDir, 'dark');

        $this->assertSame('light', $theme->key);
    }

    public function testThemeFileMissingThrows(): void
    {
        $this->writeManifest(['default' => 'light', 'available' => ['light', 'dark']]);
        $this->writeTheme('light', $this->validThemeData());

        $this->expectException(\RuntimeException::class);
        ThemeLoader::load($this->tmpDir, 'dark');
    }

    public function testThemeInvalidJsonThrows(): void
    {
        $this->writeManifest(['default' => 'light', 'available' => ['light']]);
        file_put_contents($this->tmpDir . DIRECTORY_SEPARATOR . 'light.json', '{invalid');

        $this->expectException(\RuntimeException::class);
        ThemeLoader::load($this->tmpDir, 'light');
    }

    public function testThemeMissingFieldThrows(): void
    {
        $this->writeManifest(['default' => 'light', 'available' => ['light']]);
        $data = $this->validThemeData();
        unset($data['colors']);
        $this->writeTheme('light', $data);

        $this->expectException(\RuntimeException::class);
        ThemeLoader::load($this->tmpDir, 'light');
    }

    public function testEnvOverrideWorks(): void
    {
        $this->writeManifest(['default' => 'light', 'available' => ['light', 'dark']]);
        $this->writeTheme('light', $this->validThemeData());
        $dark = $this->validThemeData();
        $dark['name'] = 'Escuro';
        $this->writeTheme('dark', $dark);

        putenv('APP_THEME=dark');
        $_ENV['APP_THEME'] = 'dark';

        $theme = ThemeLoader::load($this->tmpDir);

        $this->assertSame('dark', $theme->key);
    }

    public function testToCssVariablesContainsExpectedVars(): void
    {
        $theme = Theme::fromArray('light', $this->validThemeData());

        $css = $theme->toCssVariables();

        $this->assertStringContainsString('--font-family-base:', $css);
        $this->assertStringContainsString('--font-size-base:', $css);
        $this->assertStringContainsString('--color-bg:', $css);
        $this->assertStringContainsString('--dashboard-disponivel:', $css);
    }

    public function testAvailableThemesReturnsManifestList(): void
    {
        $this->writeManifest(['default' => 'light', 'available' => ['light', 'dark']]);

        $available = ThemeLoader::availableThemes($this->tmpDir);

        $this->assertSame(['light', 'dark'], $available);
    }

    public function testRealThemesLoad(): void
    {
        $themesDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'themes';

        $available = ThemeLoader::availableThemes($themesDir);
        $this->assertNotEmpty($available);

        foreach ($available as $key) {
            $theme = ThemeLoader::load($themesDir, $key);
            $this->assertSame($key, $theme->key);
            $this->assertNotEmpty($theme->colors);
            $this->assertNotEmpty($theme->dashboard);
        }
    }
}
