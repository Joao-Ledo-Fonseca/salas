<?php
namespace Salas\Core\Http;

class ViewRenderer
{
    public static function render(string $view, array $data = [], ?string $layout = null): void
    {
        if (!array_key_exists('cfg', $data)) {
            $data['cfg'] = $GLOBALS['cfg'] ?? null;
        }
        extract($data, EXTR_SKIP);

        $viewPath = __DIR__ . '/../../views/' . ltrim($view, '/') . (substr(ltrim($view, '/'), -4) === '.php' ? '' : '.php');
        if (!is_file($viewPath)) {
            throw new \RuntimeException('Vista n�o encontrada: ' . $view);
        }

        if ($layout === null) {
            require $viewPath;
            return;
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        $layoutPath = __DIR__ . '/../../views/' . ltrim($layout, '/') . (substr(ltrim($layout, '/'), -4) === '.php' ? '' : '.php');
        if (!is_file($layoutPath)) {
            throw new \RuntimeException('Layout n�o encontrado: ' . $layout);
        }

        require $layoutPath;
    }
}
