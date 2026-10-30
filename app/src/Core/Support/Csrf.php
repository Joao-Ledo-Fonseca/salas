<?php
declare(strict_types=1);
namespace Salas\Core\Support;

use Salas\Core\Auth\SessionManager;

class Csrf
{
    private const SESSION_KEY = 'csrf_token';

    private static function sessions(): SessionManager
    {
        return new SessionManager();
    }

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

    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8') . '" />';
    }
}
?>