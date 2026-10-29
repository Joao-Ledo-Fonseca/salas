<?php
namespace Salas\Core\Http;

class ViewRenderer
{
    public static function render(string $view, array $data = [], ?string $layout = null): void
    {
        global $cfg;
        extract($data, EXTR_SKIP);

        $viewPath = __DIR__ . '/../../views/' . ltrim($view, '/');
        if (!is_file($viewPath)) {
            throw new \RuntimeException('Vista não encontrada: ' . $view);
        }

        if ($layout === null) {
            require $viewPath;
            return;
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        $layoutPath = __DIR__ . '/../../views/' . ltrim($layout, '/');
        if (!is_file($layoutPath)) {
            throw new \RuntimeException('Layout não encontrado: ' . $layout);
        }

        require $layoutPath;
    }
}
