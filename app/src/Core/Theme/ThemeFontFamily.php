<?php
namespace Salas\Core\Theme;

final class ThemeFontFamily
{
    /**
     * @return array<string,string>
     */
    public static function safeOptions(): array
    {
        return [
            'Arial' => 'Arial, Helvetica, sans-serif',
            'Verdana' => 'Verdana, Geneva, sans-serif',
            'Tahoma' => 'Tahoma, Geneva, sans-serif',
            'Trebuchet MS' => "'Trebuchet MS', Arial, sans-serif",
            'Georgia' => "Georgia, 'Times New Roman', serif",
            'Times New Roman' => "'Times New Roman', Times, serif",
            'Courier New' => "'Courier New', Courier, monospace",
        ];
    }

    /**
     * @return array<string,string>
     */
    public static function googleOptions(): array
    {
        return [
            'Inter' => "'Inter', sans-serif",
            'Roboto' => "'Roboto', sans-serif",
            'Open Sans' => "'Open Sans', sans-serif",
            'Lato' => "'Lato', sans-serif",
            'Montserrat' => "'Montserrat', sans-serif",
            'Poppins' => "'Poppins', sans-serif",
            'Nunito' => "'Nunito', sans-serif",
            'Source Sans 3' => "'Source Sans 3', sans-serif",
            'Raleway' => "'Raleway', sans-serif",
            'Oswald' => "'Oswald', sans-serif",
            'Merriweather' => "'Merriweather', serif",
            'Playfair Display' => "'Playfair Display', serif",
            'Ubuntu' => "'Ubuntu', sans-serif",
            'Fira Sans' => "'Fira Sans', sans-serif",
            'Work Sans' => "'Work Sans', sans-serif",
            'DM Sans' => "'DM Sans', sans-serif",
            'Manrope' => "'Manrope', sans-serif",
            'Rubik' => "'Rubik', sans-serif",
            'Quicksand' => "'Quicksand', sans-serif",
            'Libre Baskerville' => "'Libre Baskerville', serif",
        ];
    }

    /**
     * @return array<string,string>
     */
    public static function options(): array
    {
        return self::safeOptions() + self::googleOptions();
    }

    public static function selectionFromStack(string $fontFamily): string
    {
        $primary = trim(explode(',', $fontFamily, 2)[0], " \t\n\r\0\x0B\"'");
        foreach (array_keys(self::options()) as $option) {
            if (strcasecmp($primary, $option) === 0) {
                return $option;
            }
        }

        return 'Arial';
    }

    public static function stackForSelection(string $selection): ?string
    {
        return self::options()[$selection] ?? null;
    }

    public static function googleFontsUrlForSelection(string $selection): ?string
    {
        if (!isset(self::googleOptions()[$selection])) {
            return null;
        }

        return 'https://fonts.googleapis.com/css2?family='
            . rawurlencode($selection . ':wght@300;400;500;600;700')
            . '&display=swap';
    }

    public static function googleFontsUrlForStack(string $fontFamily): ?string
    {
        $selection = self::selectionFromStack($fontFamily);
        if (strcasecmp(trim(explode(',', $fontFamily, 2)[0], " \t\n\r\0\x0B\"'"), $selection) !== 0) {
            return null;
        }

        return self::googleFontsUrlForSelection($selection);
    }
}
