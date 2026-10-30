<?php
namespace Salas\Config {
	// Autoloader manual (sem Composer).
	\spl_autoload_register(function (string $class): void {
		$prefixes = [
			'Salas\\Tests\\' => dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'Tests' . DIRECTORY_SEPARATOR,
			'Salas\\' => dirname(__DIR__) . DIRECTORY_SEPARATOR,
		];
		foreach ($prefixes as $prefix => $baseDir) {
			if (strncmp($class, $prefix, strlen($prefix)) === 0) {
				$relative = substr($class, strlen($prefix));
				$file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
				if (is_file($file)) {
					require_once $file;
				}
				return;
			}
		}
	});

	$appConfig = AppConfig::fromFile(
		dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config.json',
		(string) ($_SERVER['SCRIPT_NAME'] ?? '/')
	);

	$GLOBALS['appConfig'] = $appConfig;

	$themesDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'themes';
	$requestedTheme = $_GET['theme'] ?? $appConfig->theme() ?? null;

	// Mantém disponíveis as cores usadas pela vista do dashboard.
	global $cfg;
	$cfg = new \stdClass();
	$cfg->theme = \Salas\Core\Theme\ThemeLoader::load($themesDir, $requestedTheme);
	$cfg->dashboard_colors = (object) $cfg->theme->dashboard;
	$cfg->availableThemes = \Salas\Core\Theme\ThemeLoader::availableThemes($themesDir);

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
