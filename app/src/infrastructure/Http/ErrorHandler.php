<?php

declare(strict_types=1);

namespace Salas\Http;

class ErrorHandler
{
    public static function register(): void
    {
        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
    }

    public static function handleException(\Throwable $exception): void
    {
        $isAjax = strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';

        if (!headers_sent()) {
            http_response_code(500);
            if ($isAjax) {
                header('Content-Type: application/json; charset=UTF-8');
            } else {
                header('Content-Type: text/html; charset=UTF-8');
            }
        }

        $message = 'Ocorreu um erro inesperado. Tente novamente mais tarde.';
        if ($exception instanceof \PDOException) {
            $message = 'Não foi possível concluir a operação devido a um erro de base de dados.';
        }

        error_log($exception->getMessage() . "\n" . $exception->getTraceAsString());

        if ($isAjax) {
            echo json_encode(['error' => $message], JSON_UNESCAPED_UNICODE);
            return;
        }

        echo '<!DOCTYPE html><html lang="pt-PT"><head><meta charset="utf-8"><title>Erro</title></head><body>';
        echo '<h1>Erro</h1><p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
        echo '</body></html>';
    }

    public static function handleError(int $severity, string $message, string $file, int $line): bool
    {
        if (!in_array($severity, [E_ERROR, E_WARNING, E_USER_ERROR, E_RECOVERABLE_ERROR], true)) {
            return false;
        }

        if (!(error_reporting() & $severity)) {
            return false;
        }

        throw new \ErrorException($message, 0, $severity, $file, $line);
    }
}
