<?php
namespace Salas\Core\Theme;

/**
 * Serviço para edição e gestão de temas.
 */
class ThemeEditorService
{
    private string $themesDir;

    public function __construct(string $themesDir)
    {
        $this->themesDir = $themesDir;
    }

    public function getThemeData(string $key): array
    {
        $path = $this->themesDir . DIRECTORY_SEPARATOR . $key . '.json';
        if (is_file($path)) {
            $raw = file_get_contents($path);
            $decoded = is_string($raw) ? json_decode($raw, true) : null;
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    public function saveTheme(string $key, array $data): bool
    {
        $path = $this->themesDir . DIRECTORY_SEPARATOR . $key . '.json';
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return $json !== false && file_put_contents($path, $json . "\n") !== false;
    }

    public function themeExists(string $key): bool
    {
        return is_file($this->themesDir . DIRECTORY_SEPARATOR . $key . '.json');
    }

    public function deleteTheme(string $key): bool
    {
        if (in_array($key, ['light', 'dark'], true)) {
            return false;
        }
        $path = $this->themesDir . DIRECTORY_SEPARATOR . $key . '.json';
        return is_file($path) && unlink($path);
    }

    public function updateManifest(string $key, bool $add = true): bool
    {
        $manifestPath = $this->themesDir . DIRECTORY_SEPARATOR . 'themes.json';
        if (!is_file($manifestPath)) return false;

        $raw = file_get_contents($manifestPath);
        $manifest = is_string($raw) ? json_decode($raw, true) : null;
        if (!is_array($manifest) || !isset($manifest['available'])) return false;

        if ($add) {
            if (!in_array($key, $manifest['available'], true)) {
                $manifest['available'][] = $key;
                $manifest['available'] = array_values(array_unique($manifest['available']));
            }
        } else {
            $manifest['available'] = array_values(array_diff($manifest['available'], [$key]));
        }

        $json = json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return $json !== false && file_put_contents($manifestPath, $json . "\n") !== false;
    }

    public function sanitizeKey(string $key): ?string
    {
        $key = trim($key);
        if ($key === '') return null;
        $key = preg_replace('/\s+/', '-', $key);
        $key = preg_replace('/[^a-zA-Z0-9_-]/', '', $key);
        $key = strtolower($key);
        return (strlen($key) >= 3 && strlen($key) <= 32) ? $key : null;
    }
}
