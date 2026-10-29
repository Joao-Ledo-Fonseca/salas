<?php
namespace Salas\Config {
	require_once dirname(__DIR__, 3) . '/vendor/autoload.php';

	$appConfig = AppConfig::fromFile(
		dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config.json',
		(string) ($_SERVER['SCRIPT_NAME'] ?? '/')
	);

	// Mantém disponíveis apenas as cores usadas pela vista do dashboard.
	global $cfg;
	$cfg = new \stdClass();
	$cfg->dashboard_colors = (object) [
		'reservado' => '#FBFABB',
		'confirmado' => '#BFF9B9',
		'cancelado' => '#FCC5C5',
		'disponivel' => '#FFFFFF',
		'disponivel_hover' => '#DFDFDF',
		'selected' => '#BFF9B9',
		'cell_border' => '#E6E6E6',
		'cell_text' => '#333333',
	];

	setlocale(LC_TIME, 'portuguese');
	date_default_timezone_set('Europe/Lisbon');
}

namespace {
	if (!function_exists('app_url')) {
		function app_url(string $path = ''): string
		{
			global $appConfig;
			return $appConfig->urlBasePath() . '/' . ltrim($path, '/');
		}
	}
}
