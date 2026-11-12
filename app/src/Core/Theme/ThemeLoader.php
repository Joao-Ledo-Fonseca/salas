<?php
namespace Salas\Core\Theme;

use Salas\Core\Theme\Theme;

final class ThemeLoader
{
    public static function load(string $themesDir, ?string $requestedTheme = null): Theme
    {
        $themesJson = file_get_contents($themesDir . '/themes.json');
        if ($themesJson === false) {
            throw new \RuntimeException("Could not read themes.json");
        }

        $themesData = json_decode($themesJson, true);
        if ($themesData === null) {
            throw new \RuntimeException("Could not decode themes.json");
        }

        $themeName = $requestedTheme ?? getenv('APP_THEME') ?? $_ENV['APP_THEME'] ?? $themesData['default'] ?? null;
        if ($themeName === null) {
            throw new \RuntimeException("Theme name not found");
        }

        $themeFile = $themesDir . '/' . $themeName . '.json';
        if (!is_file($themeFile)) {
            // Se o tema solicitado não existir, tenta carregar o padrão
            $defaultTheme = $themesData['default'] ?? null;
            $themeFile = $themesDir . '/' . $defaultTheme . '.json';
            
            if ($defaultTheme === null || !is_file($themeFile)) {
                throw new \RuntimeException("Theme file not found: " . $themeFile);
            }
            $themeName = $defaultTheme;
        }

        $themeContent = file_get_contents($themeFile);
        if ($themeContent === false) {
            throw new \RuntimeException("Could not read theme file: " . $themeFile);
        }

        $themeData = json_decode($themeContent, true);
        if ($themeData === null) {
            throw new \RuntimeException("Could not decode theme file: " . $themeFile);
        }

        // Apenas cores declaradas no mapa autoritário são aceites.
        // Chaves não declaradas são descartadas.
        if (!isset($themeData['colors'])) {
            throw new \RuntimeException("Theme missing colors field");
        }
        
        $colors = ThemeColorManager::validateColors($themeData['colors'] ?? []);
        $dashboard = ThemeColorManager::validateDashboard($themeData['dashboard'] ?? []);

        return new Theme(
            key: $themeName,
            name: $themeData['name'] ?? '',
            fontFamily: $themeData['fontFamily'] ?? '',
            fontSizeBase: $themeData['fontSizeBase'] ?? '',
            baseColor: $themeData['baseColor'] ?? '',
            colors: $colors,
            dashboard: $dashboard
        );
    }

    public static function availableThemes(string $themesDir): array
    {
        $themesJson = file_get_contents($themesDir . '/themes.json');
        if ($themesJson === false) {
            return [];
        }

        $themesData = json_decode($themesJson, true);
        if ($themesData === null) {
            return [];
        }

        return $themesData['available'] ?? [];
    }
}