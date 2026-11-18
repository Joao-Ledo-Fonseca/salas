<?php

namespace Salas\Core\Theme;

/**
 * Classe Color.
 */
final class Color
{
    private float $r, $g, $b;

    private function __construct(float $r, float $g, float $b)
    {
        $this->r = max(0, min(255, $r));
        $this->g = max(0, min(255, $g));
        $this->b = max(0, min(255, $b));
    }

    public static function fromHex(string $hex): self
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        return new self(
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2))
        );
    }

    public static function fromHsl(float $h, float $s, float $l): self
    {
        $h = fmod(fmod($h, 360) + 360, 360) / 360;
        if ($s === 0.0) {
            $r = $g = $b = $l;
        } else {
            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;
            $hue = function ($t) use ($p, $q) {
                if ($t < 0) $t += 1;
                if ($t > 1) $t -= 1;
                if ($t < 1 / 6) return $p + ($q - $p) * 6 * $t;
                if ($t < 1 / 2) return $q;
                if ($t < 2 / 3) return $p + ($q - $p) * (2 / 3 - $t) * 6;
                return $p;
            };
            $r = $hue($h + 1 / 3);
            $g = $hue($h);
            $b = $hue($h - 1 / 3);
        }
        return new self($r * 255, $g * 255, $b * 255);
    }

    public function toHex(): string
    {
        return sprintf("#%02X%02X%02X", round($this->r), round($this->g), round($this->b));
    }

    public function toHsl(): array
    {
        $r = $this->r / 255;
        $g = $this->g / 255;
        $b = $this->b / 255;
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $h = 0;
        $s = 0;
        $l = ($max + $min) / 2;
        if ($max !== $min) {
            $delta = $max - $min;
            $s = $l > 0.5 ? $delta / (2 - $max - $min) : $delta / ($max + $min);
            if ($max === $r) $h = ($g - $b) / $delta + ($g < $b ? 6 : 0);
            elseif ($max === $g) $h = ($b - $r) / $delta + 2;
            else $h = ($r - $g) / $delta + 4;
            $h /= 6;
        }
        return ['h' => $h * 360, 's' => $s, 'l' => $l];
    }

    public function getRelativeLuminance(): float
    {
        $channels = array_map(function ($c) {
            $v = $c / 255;
            return $v <= 0.04045 ? $v / 12.92 : pow(($v + 0.055) / 1.055, 2.4);
        }, ['r' => $this->r, 'g' => $this->g, 'b' => $this->b]);
        return 0.2126 * $channels['r'] + 0.7152 * $channels['g'] + 0.0722 * $channels['b'];
    }

    public function getContrastRatio(self $other): float
    {
        $l1 = $this->getRelativeLuminance();
        $l2 = $other->getRelativeLuminance();
        return (max($l1, $l2) + 0.05) / (min($l1, $l2) + 0.05);
    }
}
