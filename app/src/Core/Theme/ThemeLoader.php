<?php
namespace Salas\Core\Theme;

final class ThemeLoader
{
    public static function load(string $themesDir, ?string $requested = null): Theme
    {
        $manifestPath = $themesDir . DIRECTORY_SEPARATOR . 'themes.json';
        if (!is_file($manifestPath)) {
            throw new \RuntimeException('Manifesto de temas não encontrado.');
        }
        $manifestRaw = file_get_contents($manifestPath);
        if ($manifestRaw === false) {
            throw new \RuntimeException('Não foi possível ler o manifesto de temas.');
        }
        try {
            $manifest = json_decode($manifestRaw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \RuntimeException('Manifesto de temas inválido.', 0, $e);
        }
        if (!is_array($manifest) || !isset($manifest['default'], $manifest['available']) || !is_array($manifest['available'])) {
            throw new \RuntimeException('Manifesto de temas inválido.');
        }

        $key = $requested;
        if ($key === null || $key === '') {
            $env = getenv('APP_THEME');
            if (is_string($env) && $env !== '') {
                $key = $env;
            }
        }
        if ($key === null || $key === '') {
            $key = (string)$manifest['default'];
        }
        if (!in_array($key, $manifest['available'], true)) {
            $key = (string)$manifest['default'];
        }

        $file = $themesDir . DIRECTORY_SEPARATOR . $key . '.json';
        if (!is_file($file)) {
            throw new \RuntimeException('Tema não encontrado: ' . $key);
        }
        $raw = file_get_contents($file);
        if ($raw === false) {
            throw new \RuntimeException('Não foi possível ler o tema: ' . $key);
        }
        try {
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \RuntimeException('Tema inválido: ' . $key, 0, $e);
        }
        if (!is_array($data)) {
            throw new \RuntimeException('Tema inválido: ' . $key);
        }
        return Theme::fromArray($key, $data);
    }
	public static function availableThemes(string $themesDir): array
	{
		$manifestPath = $themesDir . DIRECTORY_SEPARATOR . 'themes.json';
		if (!is_file($manifestPath)) {
			throw new \RuntimeException('Manifesto de temas não encontrado.');
		}
		$manifestRaw = file_get_contents($manifestPath);
		if ($manifestRaw === false) {
			throw new \RuntimeException('Não foi possível ler o manifesto de temas.');
		}
		try {
			$manifest = json_decode($manifestRaw, true, 512, JSON_THROW_ON_ERROR);
		} catch (\JsonException $e) {
			throw new \RuntimeException('Manifesto de temas inválido.', 0, $e);
		}
		if (!is_array($manifest) || !isset($manifest['default'], $manifest['available']) || !is_array($manifest['available'])) {
			throw new \RuntimeException('Manifesto de temas inválido.');
		}
		$available = [];
		foreach ($manifest['available'] as $key) {
			if (is_string($key) && $key !== '') {
				$available[] = $key;
			}
		}
		return array_values(array_unique($available));
	}

}
