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
        'baseColor'     => ['--color-base',          'baseColor',     'Cor base',                     'Cor de ref. para geração da paleta'],
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
        'menuHoverText' => ['--color-menu-hover-text', 'menuHoverText', 'Texto hover menu',             '.menu li:hover'],
        'menuText'      => ['--color-menu-text',     'menuText',      'Texto do menu',                '.menu, .menu a'],
    ];

    public const DASHBOARD_MAP = [
        'reservado'     => ['--dashboard-reservado',     'reservado',     'Célula reservada',               '.reservado cell'],
        'confirmado'    => ['--dashboard-confirmado',    'confirmado',    'Célula confirmada',              '.confirmado cell'],
        'cancelado'     => ['--dashboard-cancelado',     'cancelado',     'Célula cancelada',               '.cancelado cell'],
        'disponivel'    => ['--dashboard-disponivel',    'disponivel',    'Célula disponível',              '.disponivel cell'],
        'disponivelHover' => ['--dashboard-disponivel-hover', 'disponivelHover', 'Hover célula disponível',    '.disponivel:hover'],
        'selected'      => ['--dashboard-selected',      'selected',      'Célula selecionada',             '.selected cell'],
        'cellBorder'    => ['--dashboard-cell-border',   'cellBorder',    'Borda da célula',                'table th/td, hr'],
        'cellText'      => ['--dashboard-cell-text',     'cellText',      'Texto da célula',                'table td'],
    ];

    
    /**
     * Verifica se a chave JSON é válida para cores.
     *
     * @param string $jsonKey A chave JSON a ser verificada.
     * @return bool Retorna true se a chave for válida, caso contrário false.
     */
    public static function isValidColor(string $jsonKey): bool
    {
        return array_key_exists($jsonKey, self::COLOR_MAP);
    }

    /**
     * Verifica se a chave JSON é válida para dashboard.
     *
     * @param string $jsonKey A chave JSON a ser verificada.
     * @return bool Retorna true se a chave for válida, caso contrário false.
     */
    public static function isValidDashboard(string $jsonKey): bool
    {
        return array_key_exists($jsonKey, self::DASHBOARD_MAP);
    }
    
    /**
     * Retorna a variável CSS correspondente à chave JSON.
     *
     * @param string $jsonKey A chave JSON.
     * @return string|null Retorna a variável CSS correspondente ou null se não for válida.
     */
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

   /*
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

    /**
     * Retorna o nome interno (camelCase) correspondente à chave JSON.
     *
     * @param string $jsonKey A chave JSON.
     * @return string|null Retorna o nome interno correspondente ou null se não for válida.
     */
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

    /**
     * Retorna o label (descritivo) correspondente à chave JSON.
     *
     * @param string $jsonKey A chave JSON.
     * @return string|null Retorna o label correspondente ou null se não for válida.
     */
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

    /**
     * Retorna o uso (comentário) correspondente à chave JSON.
     *
     * @param string $jsonKey A chave JSON.
     * @return string|null Retorna o uso correspondente ou null se não for válida.
     */
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

    /**
     * Retorna todas as chaves JSON válidas para cores.
     *
     * @return array Retorna um array com todas as chaves JSON válidas para cores.
     */
    public static function allColorKeys(): array
    {
        return array_keys(self::COLOR_MAP);
    }

    /**
     * Retorna todas as chaves JSON válidas para dashboard.
     *
     * @return array Retorna um array com todas as chaves JSON válidas para dashboard.
     */
    public static function getMenuKeys(): array
    {
        return array_filter(self::allColorKeys(), fn($k) => strpos($k, 'menu') === 0);
    }

    /**
     * Retorna todas as chaves JSON válidas para avisos.
     *
     * @return array Retorna um array com todas as chaves JSON válidas para avisos.
     */
    public static function getAvisosKeys(): array
    {
        return ['surfaceAlt', 'borderStrong', 'success', 'warning', 'danger', 'dangerDark', 'dangerLight', 'dangerBorder'];
    }

    /**
     * Retorna todas as chaves JSON válidas para a aplicação (excluindo menu e avisos).
     *
     * @return array Retorna um array com todas as chaves JSON válidas para a aplicação.
     */
    public static function getAppKeys(): array
    {
        $menu = self::getMenuKeys();
        $avisos = self::getAvisosKeys();
        // 'baseColor' é apresentada na aba Base de Temas.php, não na aba Gerais (app).
        $excluidas = array_merge($menu, $avisos, ['baseColor']);
        return array_filter(self::allColorKeys(), fn($k) => !in_array($k, $excluidas, true));
    }

    /**
     * Retorna todas as chaves JSON válidas para a dashboard.
     *
     * @return array Retorna um array com todas as chaves JSON válidas para a dashboard.
     */
    public static function allDashboardKeys(): array
    {
        return array_keys(self::DASHBOARD_MAP);
    }

    /**
     * Valida um array de cores, removendo chaves inválidas.
     *
     * @param array $colors O array de cores a ser validado.
     * @return array Retorna um array contendo apenas as cores válidas.
     */
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

    /**
     * Valida um array de dashboard, removendo chaves inválidas.
     *
     * @param array $dashboard O array de dashboard a ser validado.
     * @return array Retorna um array contendo apenas as chaves válidas para dashboard.
     */
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

    /*
     * Gera variáveis CSS a partir de um array de cores.
     *
     * @param array $colors O array de cores.
     * @return string Retorna uma string contendo as variáveis CSS geradas.
     */
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


    /**
     * Gera variáveis CSS para o dashboard a partir de um array de cores.
     *
     * @param array $dashboard O array de cores do dashboard.
     * @return string Retorna uma string contendo as variáveis CSS geradas.
     */
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

    /**
     * Gera variáveis CSS completas (cores e dashboard) a partir de arrays de cores.
     *
     * @param array $colors O array de cores.
     * @param array $dashboard O array de cores do dashboard.
     * @return string Retorna uma string contendo todas as variáveis CSS geradas.
     */
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

    /**
     * Converte uma chave camelCase para kebab-case.
     *
     * @param string $key A chave em camelCase.
     * @return string Retorna a chave convertida para kebab-case.
     */
    public static function kebab(string $key): string
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $key));
    }
}
