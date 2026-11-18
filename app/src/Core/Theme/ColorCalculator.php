<?php

namespace Salas\Core\Theme;

/**
 * Classe ColorCalculator.
 */
class ColorCalculator
{
    public static function calculatePalette(string $baseHex, array $rules = []): array
    {
        $rules = array_merge([
            'surfaceSaturationFactor' => 0.22,
            'surfaceSaturationMax' => 0.12,
            'primaryLuminance' => 0.34,
            'primarySaturationMin' => 0.52,
            'primaryDarkOffset' => 0.08,
            'primaryLightLuminance' => 0.78,
            'primaryLinkSaturationMin' => 0.55,
            'menuSaturationMin' => 0.04,
            'semanticSaturation' => 0.42
        ], $rules);

        $base = Color::fromHex($baseHex);
        $hsl = $base->toHsl();
        $isLight = $base->getRelativeLuminance() >= 0.179;
        $text = self::contrastingText($base);

        $surfaceHue = $hsl['h'];
        $surfaceSaturation = min($hsl['s'] * $rules['surfaceSaturationFactor'], $rules['surfaceSaturationMax']);

        $surface = Color::fromHsl($surfaceHue, $surfaceSaturation, $isLight ? 0.96 : 0.17);
        $surfaceAlt = Color::fromHsl($surfaceHue, $surfaceSaturation, $isLight ? 0.90 : 0.24);
        $border = Color::fromHsl($surfaceHue, max($surfaceSaturation, $rules['menuSaturationMin']), $isLight ? 0.78 : 0.38);
        $borderStrong = Color::fromHsl($surfaceHue, max($surfaceSaturation, $rules['menuSaturationMin']), $isLight ? 0.62 : 0.53);

        $primary = Color::fromHsl($hsl['h'], max($hsl['s'], $rules['primarySaturationMin']), $rules['primaryLuminance']);
        while ($primary->getContrastRatio(Color::fromHex('#FFFFFF')) < 4.5 && $primary->toHsl()['l'] > 0.12) {
            $pL = $primary->toHsl()['l'] - 0.01;
            $primary = Color::fromHsl($hsl['h'], max($hsl['s'], $rules['primarySaturationMin']), $pL);
        }

        $primaryDark = Color::fromHsl($hsl['h'], max($hsl['s'], $rules['primarySaturationMin']), max(0.1, $primary->toHsl()['l'] - $rules['primaryDarkOffset']));
        $primaryLight = Color::fromHsl($hsl['h'], max($hsl['s'] * 0.7, 0.25), $rules['primaryLightLuminance']);
        $primaryLink = self::findReadableHue($hsl['h'], max($hsl['s'], $rules['primaryLinkSaturationMin']), $base, !$isLight);

        $menuBg = Color::fromHsl($hsl['h'], max($surfaceSaturation, $rules['menuSaturationMin']), $isLight ? 0.14 : 0.07);
        $menuItem = Color::fromHsl($hsl['h'], max($surfaceSaturation, $rules['menuSaturationMin']), $isLight ? 0.23 : 0.14);
        $menuHoverBg = Color::fromHsl($hsl['h'], max($surfaceSaturation, $rules['menuSaturationMin']), $isLight ? 0.78 : 0.30);
        $overlay = (self::contrastingText($base)->toHex() === '#F4F6F8') ? Color::fromHex('#F4F6F8') : Color::fromHex('#171A1F');

        $semanticLightness = $isLight ? 0.90 : 0.24;

        return [
            'bg' => $base->toHex(),
            'surface' => $surface->toHex(),
            'surfaceAlt' => $surfaceAlt->toHex(),
            'text' => $text->toHex(),
            'textStrong' => $text->toHex(),
            'textMuted' => self::findReadableHue($surfaceHue, 0.05, $base, !$isLight)->toHex(),
            'border' => $border->toHex(),
            'borderStrong' => $borderStrong->toHex(),
            'primary' => $primary->toHex(),
            'primaryDark' => $primaryDark->toHex(),
            'primaryLight' => $primaryLight->toHex(),
            'primaryLink' => $primaryLink->toHex(),
            'success' => Color::fromHsl(135, $rules['semanticSaturation'], $semanticLightness)->toHex(),
            'warning' => Color::fromHsl(45, $rules['semanticSaturation'], $semanticLightness)->toHex(),
            'danger' => Color::fromHsl(355, $rules['semanticSaturation'], $semanticLightness)->toHex(),
            'dangerDark' => self::findReadableHue(355, 0.72, $isLight ? Color::fromHex('#F5DDDF') : Color::fromHex('#3B2024'), !$isLight)->toHex(),
            'dangerLight' => Color::fromHsl(355, 0.28, $isLight ? 0.95 : 0.18)->toHex(),
            'dangerBorder' => Color::fromHsl(355, $rules['semanticSaturation'], $isLight ? 0.70 : 0.42)->toHex(),
            'menuBg' => $menuBg->toHex(),
            'menuItem' => $menuItem->toHex(),
            'menuHoverBg' => $menuHoverBg->toHex(),
            'menuHoverText' => self::contrastingText($menuHoverBg)->toHex(),
            'menuText' => self::contrastingText($menuBg)->toHex(),
            'overlay' => $overlay->toHex()
        ];
    }

    private static function contrastingText(Color $bg): Color
    {
        return $bg->getContrastRatio(Color::fromHex('#000000')) >= $bg->getContrastRatio(Color::fromHex('#FFFFFF'))
            ? Color::fromHex('#171A1F')
            : Color::fromHex('#F4F6F8');
    }

    private static function findReadableHue(float $h, float $s, Color $bg, bool $preferLight): Color
    {
        $low = 0.02;
        $high = 0.98;
        $bestCandidate = null;

        // Busca binária para encontrar a luminosidade que atinge o contraste mínimo
        for ($i = 0; $i < 10; $i++) {
            $mid = ($low + $high) / 2;
            $candidate = Color::fromHsl($h, $s, $mid);

            if ($candidate->getContrastRatio($bg) >= 4.5) {
                $bestCandidate = $candidate;
                if ($preferLight) {
                    $high = $mid; // Tenta encontrar um valor mais baixo (mais escuro)
                } else {
                    $low = $mid; // Tenta encontrar um valor mais alto (mais claro)
                }
            } else {
                // Se não atingir o contraste, precisa ajustar a luminosidade
                // Esta lógica simplificada assume que o contraste aumenta ao afastar da luminância do fundo
                if ($mid < $bg->toHsl()['l']) {
                    $low = $mid;
                } else {
                    $high = $mid;
                }
            }
        }

        return $bestCandidate ?? self::contrastingText($bg);
    }
}
