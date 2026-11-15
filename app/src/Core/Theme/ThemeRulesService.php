<?php

namespace Salas\Core\Theme;

class ThemeRulesService
{
    private string $rulesPath;

    public function __construct(string $rulesPath)
    {
        $this->rulesPath = $rulesPath;
    }

    public function getRules(): array
    {
        if (!is_file($this->rulesPath)) {
            return [];
        }
        return json_decode(file_get_contents($this->rulesPath), true) ?? [];
    }

    public function saveRules(array $rules): bool
    {
        return file_put_contents($this->rulesPath, json_encode($rules, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n") !== false;
    }
}
