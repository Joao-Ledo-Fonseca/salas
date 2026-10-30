<?php
namespace Salas\Core\Theme;

final class Theme
{
    public string $key;
    public string $name;
    public string $fontFamily;
    public string $fontSizeBase;
    /** @var array<string,string> */
    public array $colors;
    /** @var array<string,string> */
    public array $dashboard;

    public function __construct(
        string $key,
        string $name,
        string $fontFamily,
        string $fontSizeBase,
        array $colors,
        array $dashboard
    ) {
        $this->key = $key;
        $this->name = $name;
        $this->fontFamily = $fontFamily;
        $this->fontSizeBase = $fontSizeBase;
        $this->colors = $colors;
        $this->dashboard = $dashboard;
    }

    public static function fromArray(string $key, array $data): self
    {
        foreach (['name', 'fontFamily', 'fontSizeBase', 'colors', 'dashboard'] as $field) {
            if (!isset($data[$field])) {
                throw new \RuntimeException("Tema invÃ¡lido: falta '$field'.");
            }
        }
        if (!is_array($data['colors']) || !is_array($data['dashboard'])) {
            throw new \RuntimeException('Tema invÃ¡lido: colors/dashboard tÃªm de ser objetos.');
        }
        return new self(
            $key,
            (string)$data['name'],
            (string)$data['fontFamily'],
            (string)$data['fontSizeBase'],
            $data['colors'],
            $data['dashboard']
        );
    }

    public function toCssVariables(): string
    {
        $vars = [];
        $vars[] = '--font-family-base: ' . $this->fontFamily . ';';
        $vars[] = '--font-size-base: ' . $this->fontSizeBase . ';';
        foreach ($this->colors as $k => $v) {
            $vars[] = '--color-' . $this->kebab($k) . ': ' . $v . ';';
        }
        foreach ($this->dashboard as $k => $v) {
            $vars[] = '--dashboard-' . $this->kebab($k) . ': ' . $v . ';';
        }
        return implode("\n    ", $vars);
    }

    private function kebab(string $key): string
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $key));
    }
}
