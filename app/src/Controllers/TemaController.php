<?php

namespace Salas\Controllers;

use Salas\Core\Theme\ThemeLoader;
use Salas\Core\Theme\ThemeColorManager;
use Salas\Core\Theme\ThemeFontFamily;
use Salas\Core\Theme\ThemeEditorService;
use Salas\Core\Persistence\Database;
use Salas\Core\Support\Csrf;
use Salas\Core\Validation\Validator;
use Salas\Core\Theme\ColorCalculator;
use Salas\Core\Theme\ThemeRulesService;

/**
 * Classe TemaController.
 */
class TemaController
{
    private Database $db;
    private ThemeEditorService $editor;
    private string $themesDir;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->themesDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'themes';
        $this->editor = new ThemeEditorService($this->themesDir);
    }

    public function temasForm(array $query, array $post = []): array
    {
        global $cfg;
        $available = ThemeLoader::availableThemes($this->themesDir);
        $errormsg = (string)($query['errormsg'] ?? '');

        $activeKey = (string)($query['theme'] ?? '');
        if ($activeKey === '' && isset($cfg->theme)) {
            $activeKey = (string)$cfg->theme->key;
        }
        if ($activeKey === '' || !in_array($activeKey, $available, true)) {
            $activeKey = isset($cfg->theme) ? (string)$cfg->theme->key : ($available[0] ?? 'light');
        }


        if (isset($query['ajax']) && $query['ajax'] === 'calculate_palette') {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            header('Content-Type: application/json; charset=utf-8');
            $baseColor = Validator::string($query['baseColor'] ?? '#FFFFFF');

            $rulesPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'theme_rules.json';
            $rules = is_file($rulesPath) ? (json_decode((string)file_get_contents($rulesPath), true) ?? []) : [];

            $palette = ColorCalculator::calculatePalette($baseColor, $rules);

            echo json_encode($palette);
            exit();
        }

        if (isset($post['ativar_tema']) && isset($post['active_theme'])) {
            $newActive = (string)$post['active_theme'];
            if (in_array($newActive, $available, true)) {
                $this->writeConfigTheme($newActive);
                header('Location: ' . app_url('?acao=temas&theme=' . urlencode($newActive)));
                exit();
            }
        }

        if (isset($post['criar_tema'])) {
            $novoNome = $this->editor->sanitizeKey((string)($post['name'] ?? ''));
            if ($novoNome === null || $this->editor->themeExists($novoNome)) {
                header('Location: ' . app_url('?acao=temas&theme=' . urlencode($activeKey) . '&errormsg=' . urlencode('Nome inválido ou já existente.')));
                exit();
            }

            $colors = ThemeColorManager::validateColors($post['colors'] ?? []);
            $dashboard = ThemeColorManager::validateDashboard($post['dashboard'] ?? []);

            $data = [
                'name' => $post['name'],
                'fontFamily' => ThemeFontFamily::stackForSelection((string)($post['fontFamily'] ?? '')),
                'fontSizeBase' => (string)($post['fontSizeBase'] ?? '14'),
                'baseColor' => (string)($post['baseColor'] ?? '#FFFFFF'), // Atributo preservado (não alterar nome)
                'colors' => $colors,
                'dashboard' => $dashboard,
            ];

            if ($this->editor->saveTheme($novoNome, $data)) {
                $this->editor->updateManifest($novoNome, true);
                header('Location: ' . app_url('?acao=temas&theme=' . urlencode($activeKey) . '&edit_theme=' . urlencode($novoNome)));
            }
            exit();
        }

        if (isset($post['apagar_tema'])) {
            $editKey = (string)($post['edit_theme'] ?? '');
            if (in_array($editKey, ['light', 'dark'], true) || $editKey === $activeKey) {
                header('Location: ' . app_url('?acao=temas&theme=' . urlencode($activeKey) . '&edit_theme=' . urlencode($editKey) . '&errormsg=' . urlencode('Não é possível apagar o tema ativo ou os temas base.')));
                exit();
            }
            if ($this->editor->themeExists($editKey)) {
                $this->editor->deleteTheme($editKey);
                $this->editor->updateManifest($editKey, false);
            }
            header('Location: ' . app_url('?acao=temas&theme=' . urlencode($activeKey)));
            exit();
        }

        if (isset($post['gravar'])) {
            $editKey = (string)($post['edit_theme'] ?? '');
            if (in_array($editKey, $available, true)) {
                $original = $this->editor->getThemeData($editKey);
                $name = Validator::string($post['name'] ?? ($original['name'] ?? $editKey));
                $fontFamily = ThemeFontFamily::stackForSelection((string)($post['fontFamily'] ?? ''));

                $baseColor = Validator::string($post['baseColor'] ?? ($original['baseColor'] ?? '#FFFFFF'));
                $fontSizeBase = Validator::string($post['fontSizeBase'] ?? ($original['fontSizeBase'] ?? '14'));

                $colors = ThemeColorManager::validateColors($post['colors'] ?? []);
                $dashboard = ThemeColorManager::validateDashboard($post['dashboard'] ?? []);

                $data = [
                    'name' => $name,
                    'fontFamily' => $fontFamily,
                    'fontSizeBase' => $fontSizeBase,
                    'baseColor' => $baseColor,
                    'colors' => $colors,
                    'dashboard' => $dashboard,
                ];

                $this->editor->saveTheme($editKey, $data);
                header('Location: ' . app_url('?acao=temas&theme=' . urlencode($activeKey) . '&edit_theme=' . urlencode($editKey)));
                exit();
            }
        }

        $editKey = (string)($query['edit_theme'] ?? $post['edit_theme'] ?? $activeKey);
        if (!in_array($editKey, $available, true)) $editKey = $activeKey;

        $themeData = $this->editor->getThemeData($editKey);

        return [
            'activeTheme' => $activeKey,
            'availableThemes' => $available,
            'editTheme' => $editKey,
            'themeData' => $themeData,
            'colorKeys' => isset($themeData['colors']) ? array_keys($themeData['colors']) : [],
            'dashboardKeys' => isset($themeData['dashboard']) ? array_keys($themeData['dashboard']) : [],
            'safeFontFamilies' => ThemeFontFamily::safeOptions(),
            'googleFontFamilies' => ThemeFontFamily::googleOptions(),
            'fontFamilySelection' => ThemeFontFamily::selectionFromStack((string)($themeData['fontFamily'] ?? '')),
            'errormsg' => $errormsg,
            'csrfToken' => Csrf::token(),
        ];
    }

    private function writeConfigTheme(string $themeKey): void
    {
        $configPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config.json';
        if (!is_file($configPath)) return;
        $settings = json_decode(file_get_contents($configPath), true);
        if (is_array($settings)) {
            $settings['theme'] = $themeKey;
            file_put_contents($configPath, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");
        }
    }
}
