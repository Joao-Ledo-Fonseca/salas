<?php
namespace Salas\Controllers;

use Salas\Core\Theme\ThemeLoader;
use Salas\Core\Persistence\Database;
use Salas\Core\Support\Csrf;
use Salas\Core\Validation\Validator;

class TemaController
{
    private Database $db;
    private string $themesDir;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->themesDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'themes';
    }

    public function temasForm(array $query, array $post = []): array
    {
        global $cfg;
        $available = ThemeLoader::availableThemes($this->themesDir);

        $activeKey = (string)($query['theme'] ?? '');
        if ($activeKey === '' && isset($cfg->theme)) {
            $activeKey = (string)$cfg->theme->key;
        }
        if ($activeKey === '' || !in_array($activeKey, $available, true)) {
            $activeKey = isset($cfg->theme) ? (string)$cfg->theme->key : ($available[0] ?? 'light');
        }

        if (isset($post['ativar_tema']) && isset($post['active_theme'])) {
            $newActive = (string)$post['active_theme'];
            if (in_array($newActive, $available, true)) {
                $this->writeConfigTheme($newActive);
                $activeKey = $newActive;
                header('Location: ' . app_url('?acao=temas&theme=' . urlencode($activeKey)));
                exit();
            }
        }

        if (isset($post['gravar'])) {
            $editKey = (string)($post['edit_theme'] ?? '');
            if (in_array($editKey, $available, true)) {
                $themePath = $this->themesDir . DIRECTORY_SEPARATOR . $editKey . '.json';
                $original = [];
                if (is_file($themePath)) {
                    $raw = file_get_contents($themePath);
                    $decoded = is_string($raw) ? json_decode($raw, true) : null;
                    if (is_array($decoded)) {
                        $original = $decoded;
                    }
                }
                $name = Validator::string($post['name'] ?? ($original['name'] ?? $editKey));
                $fontFamily = Validator::string($post['fontFamily'] ?? ($original['fontFamily'] ?? ''));
                $fontSizeBase = Validator::string($post['fontSizeBase'] ?? ($original['fontSizeBase'] ?? '14px'));
                if (is_numeric($fontSizeBase)) {
                    $n = (int)$fontSizeBase;
                    if ($n < 8) $n = 8;
                    if ($n > 16) $n = 16;
                    $fontSizeBase = $n . 'px';
                }

                $colors = [];
                if (isset($original['colors']) && is_array($original['colors'])) {
                    foreach (array_keys($original['colors']) as $k) {
                        $colors[$k] = Validator::string($post['colors'][$k] ?? $original['colors'][$k]);
                    }
                }
                if (isset($post['colors']) && is_array($post['colors'])) {
                    foreach ($post['colors'] as $k => $v) {
                        if (is_string($k) && $k !== '' && !isset($colors[$k])) {
                            $colors[$k] = Validator::string($v);
                        }
                    }
                }

                $dashboard = [];
                if (isset($original['dashboard']) && is_array($original['dashboard'])) {
                    foreach (array_keys($original['dashboard']) as $k) {
                        $dashboard[$k] = Validator::string($post['dashboard'][$k] ?? $original['dashboard'][$k]);
                    }
                }
                if (isset($post['dashboard']) && is_array($post['dashboard'])) {
                    foreach ($post['dashboard'] as $k => $v) {
                        if (is_string($k) && $k !== '' && !isset($dashboard[$k])) {
                            $dashboard[$k] = Validator::string($v);
                        }
                    }
                }

                $data = [
                    'name' => $name,
                    'fontFamily' => $fontFamily,
                    'fontSizeBase' => $fontSizeBase,
                    'colors' => $colors,
                    'dashboard' => $dashboard,
                ];

                $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                if ($json !== false) {
                    file_put_contents($themePath, $json . "\n");
                }

                header('Location: ' . app_url('?acao=temas&theme=' . urlencode($activeKey) . '&edit_theme=' . urlencode($editKey)));
                exit();
            }
        }


        $editKey = (string)($query['edit_theme'] ?? $post['edit_theme'] ?? $activeKey);
        if (!in_array($editKey, $available, true)) {
            $editKey = $activeKey;
        }

        $themeData = [];
        $themePath = $this->themesDir . DIRECTORY_SEPARATOR . $editKey . '.json';
        if (is_file($themePath)) {
            $raw = file_get_contents($themePath);
            $decoded = is_string($raw) ? json_decode($raw, true) : null;
            if (is_array($decoded)) {
                $themeData = $decoded;
            }
        }

        $colorKeys = isset($themeData['colors']) && is_array($themeData['colors']) ? array_keys($themeData['colors']) : [];
        $dashboardKeys = isset($themeData['dashboard']) && is_array($themeData['dashboard']) ? array_keys($themeData['dashboard']) : [];

        return [
            'activeTheme' => $activeKey,
            'availableThemes' => $available,
            'editTheme' => $editKey,
            'themeData' => $themeData,
            'colorKeys' => $colorKeys,
            'dashboardKeys' => $dashboardKeys,
            'csrfToken' => Csrf::token(),
        ];
    }

    private function writeConfigTheme(string $themeKey): void
    {
        $configPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config.json';
        if (!is_file($configPath)) {
            return;
        }
        $raw = file_get_contents($configPath);
        $settings = is_string($raw) ? json_decode($raw, true) : null;
        if (!is_array($settings)) {
            return;
        }
        $settings['theme'] = $themeKey;
        $json = json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($json !== false) {
            file_put_contents($configPath, $json . "\n");
        }
    }
}
