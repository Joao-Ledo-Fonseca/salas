<?php
namespace Salas\Core\Theme;

/**
 * Classe Theme.
 */
final class Theme
{
    public string $key;
    public string $name;
    public string $fontFamily;
    public string $fontSizeBase;
    public string $baseColor;
    /** @var array<string,string> */
    public array $colors;
    /** @var array<string,string> */
    public array $dashboard;

    public function __construct(
        string $key,
        string $name,
        string $fontFamily,
        string $fontSizeBase,
        string $baseColor,
        array $colors,
        array $dashboard
    ) {
        $this->key = $key;
        $this->name = $name;
        $this->fontFamily = $fontFamily;
        $this->fontSizeBase = $fontSizeBase;
        $this->baseColor = $baseColor;
        $this->colors = $colors;
        $this->dashboard = $dashboard;
    }

    /**
     * Método fromArray.
     * @param string $key
     * @param array $data
     * @return self
     */
    public static function fromArray(string $key, array $data): self
    {
        foreach (['name', 'fontFamily', 'fontSizeBase', 'colors', 'dashboard'] as $field) {
            if (!isset($data[$field])) {
                throw new \RuntimeException("Tema inválido: falta '$field'.");
            }
        }
        if (!is_array($data['colors']) || !is_array($data['dashboard'])) {
            throw new \RuntimeException('Tema inválido: colors/dashboard têm de ser objetos.');
        }
        return new self(
            $key,
            (string)$data['name'],
            (string)$data['fontFamily'],
            (string)$data['fontSizeBase'],
            (string)($data['baseColor'] ?? $data['colors']['bg'] ?? '#FFFFFF'),
            $data['colors'],
            $data['dashboard']
        );
    }

    /**
     * Método toCssVariables.
     * @return string
     */
    public function toCssVariables(): string
    {
        $vars = [];
        $vars[] = '--font-family-base: ' . $this->fontFamily . ';';
        $vars[] = '--font-size-base: ' . $this->fontSizeBase . ';';
        $vars[] = '--color-base: ' . $this->baseColor . ';';
        foreach ($this->colors as $k => $v) {
            $vars[] = '--color-' . $this->kebab($k) . ': ' . $v . ';';
        }
        foreach ($this->dashboard as $k => $v) {
            $vars[] = '--dashboard-' . $this->kebab($k) . ': ' . $v . ';';
        }
        return implode("\n    ", $vars);
    }

    public function googleFontsUrl(): ?string
    {
        return ThemeFontFamily::googleFontsUrlForStack($this->fontFamily);
    }

    /**
     * Método kebab.
     * @param string $key
     * @return string
     */
    private function kebab(string $key): string
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $key));
    }
}
