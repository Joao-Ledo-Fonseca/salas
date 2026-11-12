<?php

namespace Salas\Core\Theme;

/**
 * ThemeColorManager
 *
 * Fonte autoritária e única de todos os nomes de cores da aplicação.
 *
 * Este mapa estabelece a correspondência obrigatória entre:
 *   - Chave JSON       : nome usado nos ficheiros .json de tema
 *   - Variável CSS     : nome da variável injetada em :root (--color-*, --dashboard-*)
 *   - Nome interno     : nome camelCase usado internamente (PHP e JS)
 *   - Label do ecrã    : descritivo apresentado no ecrã de edição Temas.php
 *   - Uso              : comentário sobre onde a cor é aplicada
 *
 * Nenhuma cor pode ser usada na aplicação se não estiver declarada aqui.
 * Todas as camadas (ThemeLoader, TemaController, Temas.php, temas_base.js)
 * consultam esta classe antes de usar qualquer nome de cor.
 */
final class ThemeColorManager
{
    public const COLOR_MAP = [
        'bg'            => ['--color-bg',            'bg',            'Fundo da página',              'body background'],
        'surface'       => ['--color-surface',       'surface',       'Fundo de superfícies/cartões', '.info-box, inputs, selects'],
        'surfaceAlt'    => ['--color-surface-alt',   'surfaceAlt',    'Fundo alternativo',            'tabelas secundárias'],
        'text'          => ['--color-text',          'text',          'Texto base',                   'body color'],
        'textStrong'    => ['--color-text-strong',   'textStrong',    'Texto forte/títulos',          'table td, .info-box'],
        'textMuted'     => ['--color-text-muted',    'textMuted',     'Texto secundário',             '.text-muted, footer'],
        'border'        => ['--color-border',        'border',        'Bordas',                       'inputs, selects, .date-input'],
        'borderStrong'  => ['--color-border-strong', 'borderStrong',  'Bordas fortes',                'bordas com destaque'],
        'primary'       => ['--color-primary',       'primary',       'Cor primária',                 'links, botões, .link'],
        'primaryDark'   => ['--color-primary-dark',  'primaryDark',   'Primária escura (hover)',      'hover de botões'],
        'primaryLight'  => ['--color-primary-light', 'primaryLight',  'Primária clara',               'focus de inputs'],
        'primaryLink'   => ['--color-primary-link',  'primaryLink',   'Hiperligações',                'table a, .link'],
        'success'       => ['--color-success',       'success',       'Sucesso',                      '.btn2'],
        'warning'       => ['--color-warning',       'warning',       'Aviso',                        'fallback --dashboard-reservado'],
        'danger'        => ['--color-danger',        'danger',        'Erro/perigo',                  '.eliminar-imagem background'],
        'dangerDark'    => ['--color-danger-dark',   'dangerDark',    'Erro escuro',                  '.form-error, .text-error'],
        'dangerLight'   => ['--color-danger-light',  'dangerLight',   'Erro claro',                   '.form-error background'],
        'dangerBorder'  => ['--color-danger-border', 'dangerBorder',  'Borda de erro',                '.form-error border'],
        'overlay'       => ['--color-overlay',       'overlay',       'Overlay/modal',                'overlay/modal'],
        'menuBg'        => ['--color-menu-bg',       'menuBg',        'Fundo do menu',                '.menu background'],
        'menuItem'      => ['--color-menu-item',     'menuItem',      'Fundo de Item do menu',        '.menu li, .menu a'],
        'menuHoverBg'   => ['--color-menu-hover-bg', 'menuHoverBg',   'Fundo hover menu',             '.menu li:hover'],
        'menuHoverText' => ['--color-menu-hover-text','menuHoverText','Texto hover menu',             '.menu li:hover'],
        'menuText'      => ['--color-menu-text',     'menuText',      'Texto do menu',                '.menu, .menu a'],
    ];

    public const DASHBOARD_MAP = [
        'reservado'     => ['--dashboard-reservado',     'reservado',     'Célula reservada',               '.reservado cell'],
        'confirmado'    => ['--dashboard-confirmado',    'confirmado',    'Célula confirmada',              '.confirmado cell'],
        'cancelado'     => ['--dashboard-cancelado',     'cancelado',     'Célula cancelada',               '.cancelado cell'],
        'disponivel'    => ['--dashboard-disponivel',    'disponivel',    'Célula disponível',              '.disponivel cell'],
        'disponivelHover'=> ['--dashboard-disponivel-hover','disponivelHover','Hover célula disponível',    '.disponivel:hover'],
        'selected'      => ['--dashboard-selected',      'selected',      'Célula selecionada',             '.selected cell'],
        'cellBorder'    => ['--dashboard-cell-border',   'cellBorder',    'Borda da célula',                'table th/td, hr'],
        'cellText'      => ['--dashboard-cell-text',     'cellText',      'Texto da célula',                'table td'],
    ];

    public static function isValidColor(string $jsonKey): bool
    {
        return array_key_exists($jsonKey, self::COLOR_MAP);
    }

    public static function isValidDashboard(string $jsonKey): bool
    {
        return array_key_exists($jsonKey, self::DASHBOARD_MAP);
    }

    public static function cssVar(string $jsonKey): ?string
    {
        if (self::isValidColor($jsonKey)) {
            return self::COLOR_MAP[$jsonKey][0];
        }
        if (self::isValidDashboard($jsonKey)) {
            return self::DASHBOARD_MAP[$jsonKey][0];
        }
        return null;
    }

    public static function jsonKey(string $cssVar): ?string
    {
        foreach (self::COLOR_MAP as $key => $entry) {
            if ($entry[0] === $cssVar) {
                return $key;
            }
        }
        foreach (self::DASHBOARD_MAP as $key => $entry) {
            if ($entry[0] === $cssVar) {
                return $key;
            }
        }
        return null;
    }

    public static function varName(string $jsonKey): ?string
    {
        if (self::isValidColor($jsonKey)) {
            return self::COLOR_MAP[$jsonKey][1];
        }
        if (self::isValidDashboard($jsonKey)) {
            return self::DASHBOARD_MAP[$jsonKey][1];
        }
        return null;
    }

    public static function label(string $jsonKey): ?string
    {
        if (self::isValidColor($jsonKey)) {
            return self::COLOR_MAP[$jsonKey][2];
        }
        if (self::isValidDashboard($jsonKey)) {
            return self::DASHBOARD_MAP[$jsonKey][2];
        }
        return null;
    }

    public static function usage(string $jsonKey): ?string
    {
        if (self::isValidColor($jsonKey)) {
            return self::COLOR_MAP[$jsonKey][3];
        }
        if (self::isValidDashboard($jsonKey)) {
            return self::DASHBOARD_MAP[$jsonKey][3];
        }
        return null;
    }

    public static function allColorKeys(): array
    {
        return array_keys(self::COLOR_MAP);
    }

    public static function getMenuKeys(): array
    {
        return array_filter(self::allColorKeys(), fn($k) => strpos($k, 'menu') === 0);
    }

    public static function getAvisosKeys(): array
    {
        return ['surfaceAlt', 'borderStrong', 'success', 'warning', 'danger', 'dangerDark', 'dangerLight', 'dangerBorder'];
    }

    public static function getAppKeys(): array
    {
        $menu = self::getMenuKeys();
        $avisos = self::getAvisosKeys();
        return array_filter(self::allColorKeys(), fn($k) => !in_array($k, $menu, true) && !in_array($k, $avisos, true));
    }

    public static function allDashboardKeys(): array
    {
        return array_keys(self::DASHBOARD_MAP);
    }

    public static function validateColors(array $colors): array
    {
        $result = [];
        foreach ($colors as $key => $value) {
            if (self::isValidColor($key)) {
                $result[$key] = is_string($value) ? $value : (string)$value;
            }
        }
        return $result;
    }

    public static function validateDashboard(array $dashboard): array
    {
        $result = [];
        foreach ($dashboard as $key => $value) {
            if (self::isValidDashboard($key)) {
                $result[$key] = is_string($value) ? $value : (string)$value;
            }
        }
        return $result;
    }

    public static function toCssVariables(array $colors): string
    {
        $vars = [];
        foreach (self::COLOR_MAP as $jsonKey => $entry) {
            $cssVar = $entry[0];
            $value = $colors[$jsonKey] ?? '';
            if ($value !== '') {
                $vars[] = $cssVar . ': ' . $value . ';';
            }
        }
        return implode("\n    ", $vars);
    }

    public static function toDashboardCssVariables(array $dashboard): string
    {
        $vars = [];
        foreach (self::DASHBOARD_MAP as $jsonKey => $entry) {
            $cssVar = $entry[0];
            $value = $dashboard[$jsonKey] ?? '';
            if ($value !== '') {
                $vars[] = $cssVar . ': ' . $value . ';';
            }
        }
        return implode("\n    ", $vars);
    }

    public static function toCssVariablesComplete(array $colors, array $dashboard): string
    {
        $parts = [];
        $colorVars = self::toCssVariables($colors);
        $dashVars = self::toDashboardCssVariables($dashboard);
        if ($colorVars !== '') {
            $parts[] = $colorVars;
        }
        if ($dashVars !== '') {
            $parts[] = $dashVars;
        }
        return implode("\n    ", $parts);
    }

    public static function kebab(string $key): string
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $key));
    }
}
