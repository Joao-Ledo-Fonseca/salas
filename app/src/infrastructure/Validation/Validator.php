<?php
namespace Salas\Infrastructure\Validation;

class Validator
{
    public static function string(mixed $value, int $maxLength = 255): string
    {
        $value = trim((string) $value);
        if ($maxLength > 0 && strlen($value) > $maxLength) {
            $value = substr($value, 0, $maxLength);
        }

        return $value;
    }

    public static function int(mixed $value, int $default = 0): int
    {
        if (is_bool($value)) {
            return $default;
        }

        $filtered = filter_var($value, FILTER_VALIDATE_INT);
        return $filtered === false ? $default : (int) $filtered;
    }

    public static function email(mixed $value): string
    {
        return filter_var(trim((string) $value), FILTER_SANITIZE_EMAIL) ?: '';
    }

    public static function escape(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
