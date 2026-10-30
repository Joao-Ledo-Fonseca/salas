<?php
namespace Salas\Core\Validation;

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
        $value = trim((string) $value);
        $filtered = filter_var($value, FILTER_VALIDATE_EMAIL);
        return $filtered === false ? '' : (string) $filtered;
    }

    public static function date(mixed $value, string $format = 'd/m/Y'): ?\DateTime
    {
        if (!is_string($value) || trim($value) === '') {
            return null;
        }
        $date = \DateTime::createFromFormat($format, trim($value));
        $errors = \DateTime::getLastErrors();
        if ($date === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
            return null;
        }
        return $date;
    }

    public static function url(mixed $value): string
    {
        $value = trim((string) $value);
        $filtered = filter_var($value, FILTER_VALIDATE_URL);
        return $filtered === false ? '' : (string) $filtered;
    }

    public static function escape(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
?>