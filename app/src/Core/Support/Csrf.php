<?php
declare(strict_types=1);
namespace Salas\Core\Support;

use Salas\Core\Auth\SessionManager;

/**
 * Classe Csrf.
 */
class Csrf
{
    private const SESSION_KEY = 'csrf_token';

    /**
     * Método sessions.
     * @return SessionManager
     */
    private static function sessions(): SessionManager
    {
        return new SessionManager();
    }

    /**
     * Método token.
     * @return string
     */
    public static function token(): string
    {
        $sessions = self::sessions();
        $sessions->start();
        $stored = $sessions->get(self::SESSION_KEY);
        if (!is_string($stored) || $stored === '') {
            $stored = bin2hex(random_bytes(32));
            $sessions->set(self::SESSION_KEY, $stored);
        }
        return $stored;
    }

    /**
     * Método validate.
     * @param ?string $token
     * @return bool
     */
    public static function validate(?string $token): bool
    {
        $sessions = self::sessions();
        $sessions->start();
        $stored = $sessions->get(self::SESSION_KEY, '');
        if (!is_string($stored) || $stored === '' || !is_string($token) || $token === '') {
            return false;
        }
        return hash_equals($stored, $token);
    }

    /**
     * Método field.
     * @return string
     */
    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8') . '" />';
    }
}
?>