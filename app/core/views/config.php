<?php

$configPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config.json';
if (!is_file($configPath) || !is_readable($configPath)) {
    throw new RuntimeException('O ficheiro de configuração da aplicação não existe ou não pode ser lido.');
}

$configJson = file_get_contents($configPath);
if ($configJson === false) {
    throw new RuntimeException('Não foi possível ler o ficheiro de configuração da aplicação.');
}

try {
    $databaseConfig = json_decode($configJson, false, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $exception) {
    throw new RuntimeException('O ficheiro de configuração da aplicação contém JSON inválido.', 0, $exception);
}

if (!$databaseConfig instanceof stdClass) {
    throw new RuntimeException('O ficheiro de configuração da aplicação tem de conter um objeto JSON.');
}

$requiredStringSettings = ['db_host', 'db_user', 'db_senha', 'db_banco'];
foreach ($requiredStringSettings as $setting) {
    if (!isset($databaseConfig->$setting) || !is_string($databaseConfig->$setting) || $databaseConfig->$setting === '') {
        throw new RuntimeException('Falta uma configuração de base de dados válida: ' . $setting . '.');
    }
}

if (!isset($databaseConfig->db_porta) || !is_int($databaseConfig->db_porta) || $databaseConfig->db_porta < 1 || $databaseConfig->db_porta > 65535) {
    throw new RuntimeException('A configuração db_porta tem de ser um inteiro entre 1 e 65535.');
}

$urlBasePath = $databaseConfig->url_base_path ?? 'auto';
if (!is_string($urlBasePath)) {
    throw new RuntimeException('A configuração url_base_path tem de ser uma string.');
}

if ($urlBasePath === 'auto') {
    $scriptDirectory = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    $urlBasePath = $scriptDirectory === '/' ? '' : rtrim($scriptDirectory, '/');
} elseif ($urlBasePath === '/') {
    $urlBasePath = '';
} elseif ($urlBasePath !== '') {
    $segments = explode('/', trim($urlBasePath, '/'));
    if ($urlBasePath[0] !== '/' || str_contains($urlBasePath, '?') || str_contains($urlBasePath, '#') || in_array('.', $segments, true) || in_array('..', $segments, true)) {
        throw new RuntimeException('A configuração url_base_path tem de ser vazia ou um caminho URL absoluto válido.');
    }
    $urlBasePath = '/' . implode('/', $segments);
}

$cfg = new stdClass();

$cfg->db_host = $databaseConfig->db_host;
$cfg->db_user = $databaseConfig->db_user;
$cfg->db_senha = $databaseConfig->db_senha;
$cfg->db_banco = $databaseConfig->db_banco;
$cfg->db_porta = $databaseConfig->db_porta;
$cfg->url_base_path = $urlBasePath;

if (!function_exists('app_url')) {
    function app_url(string $path = ''): string
    {
        global $cfg;
        return $cfg->url_base_path . '/' . ltrim($path, '/');
    }
}

// cores do dashboard
$cfg->dashboard_colors = new stdClass();
$cfg->dashboard_colors->reservado = '#FBFABB';
$cfg->dashboard_colors->confirmado = '#BFF9B9';
$cfg->dashboard_colors->cancelado = '#FCC5C5';
$cfg->dashboard_colors->disponivel = '#FFFFFF';
$cfg->dashboard_colors->disponivel_hover = '#DFDFDF';
$cfg->dashboard_colors->selected = '#BFF9B9';
$cfg->dashboard_colors->cell_border = '#E6E6E6';
$cfg->dashboard_colors->cell_text = '#333333';

setlocale(LC_TIME, 'portuguese');
date_default_timezone_set("Europe/Lisbon");

?>