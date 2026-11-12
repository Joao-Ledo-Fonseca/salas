(function () {
    'use strict';

    // Função para converter HSL para Hex
    function hslToHex(hue, saturation, lightness) {
        const h = hue / 360;
        const s = saturation;
        const l = lightness;
        const c = (1 - Math.abs(2 * l - 1)) * s;
        const x = c * (1 - Math.abs((h * 6) % 2 - 1));
        const m = l - c / 2;
        const r = Math.round((c + m) * 255);
        const g = Math.round((x + m) * 255);
        const b = Math.round((0 + m) * 255);
        return '#' + [r, g, b].map(x => {
            const hex = x.toString(16);
            return hex.length === 1 ? '0' + hex : hex;
        }).join('');
    }

    // Converte Hex para objeto HSL
function hexToHsl(hex) {
    let r = parseInt(hex.slice(1, 3), 16) / 255;
    let g = parseInt(hex.slice(3, 5), 16) / 255;
    let b = parseInt(hex.slice(5, 7), 16) / 255;
    
    const max = Math.max(r, g, b);
    const min = Math.min(r, g, b);
    let h, s, l = (max + min) / 2;
    
    if (max === min) {
        h = s = 0;
    } else {
        const d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
        switch (max) {
            case r: h = (g - b) / d + (g < b ? 6 : 0); break;
            case g: h = (b - r) / d + 2; break;
            case b: h = (r - g) / d + 4; break;
        }
        h /= 6;
    }
    
    return { h: Math.round(h * 360), s: Math.round(s * 100), l: Math.round(l * 100) };
}

// Função para encontrar um tono de contraste legível
function findReadableHue(hue, saturation, bgHex, isDark) {
    // Tenta diferentes tons até encontrar um com contraste adequado
    const bgLuminosity = 0.299 * parseInt(bgHex.slice(1, 3), 16) +
                        0.587 * parseInt(bgHex.slice(3, 5), 16) +
                        0.114 * parseInt(bgHex.slice(5, 7), 16);

    for (let i = 0; i < 360; i += 30) {
        const testHue = (hue + i) % 360;
        const testHex = hslToHex(testHue, saturation, isDark ? 0.2 : 0.8);
        const testLuminosity = 0.299 * parseInt(testHex.slice(1, 3), 16) +
                              0.587 * parseInt(testHex.slice(3, 5), 16) +
                              0.114 * parseInt(testHex.slice(5, 7), 16);

        const contrast = (bgLuminosity + 0.05) / (testLuminosity + 0.05);
        if (isDark && contrast >= 4.5) return testHex;
        if (!isDark && contrast >= 4.5) return testHex;
    }

    // Fallback: retorna o próprio fundo se nada for encontrado
    return bgHex;
}

// Converte HSL para Hex
function hslToHex(hue, saturation, lightness) {
    const h = hue / 360;
    const s = saturation;
    const l = lightness;
    const c = (1 - Math.abs(2 * l - 1)) * s;
    const x = c * (1 - Math.abs((h * 6) % 2 - 1));
    const m = l - c / 2;
    const r = Math.round((c + m) * 255);
    const g = Math.round((x + m) * 255);
    const b = Math.round((0 + m) * 255);
    return '#' + [r, g, b].map(x => {
        const hex = x.toString(16);
        return hex.length === 1 ? '0' + hex : hex;
    }).join('');
}

// Ajusta a luminosidade de uma cor HSL
function adjustLightness(hue, saturation, lightness) {
    return hslToHex(hue, saturation, lightness);
}

// Ajusta a escuridão de uma cor Hex
function adjustDarkness(hex, factor) {
    const hsl = hexToHsl(hex);
    const newLightness = Math.max(0, Math.min(1, hsl.l / 255 - factor));
    return hslToHex(hsl.h, saturation, newLightness);
}

// Ajusta o tom (hue) de uma cor
function adjustHue(hex, degrees) {
    const hsl = hexToHsl(hex);
    let newHue = (hsl.h + degrees) % 360;
    if (newHue < 0) newHue += 360;
    return hslToHex(newHue, hsl.s, hsl.l);
}

// Converte RGB para tono (hue)
function rgbToHue(hex) {
    const hsl = hexToHsl(hex);
    return hsl.h;
}

    // Critério de Fundo: Tons contidos e adequados à superfície do tema
    function semanticBg(hue) {
        return hslToHex(hue, 0.45, isLight ? 0.90 : 0.18);
    }

    // Critério de Texto: Caça ativa ao contraste usando a função findReadableHue()
    function semanticText(hue, bgHex) {
        return findReadableHue(hue, 0.75, bgHex, !isLight);
    }

    // Gera o palette completa a partir da cor base
    function updatePaletteFromBase(baseColor, isLight) {
        const hue = rgbToHue(baseColor);
        
        return {
            bgApp: semanticBg(hue),
            bgSurface: adjustLightness(hue, 0.30, isLight ? 0.90 : 0.35),
            bgSurfaceAlt: adjustLightness(hue, 0.35, isLight ? 0.85 : 0.30),
            textMain: semanticText(hue, semanticBg(hue)),
            textStrong: semanticText(hue, semanticBg(hue)),
            textMuted: adjustLightness(hue, 0.50, isLight ? 0.55 : 0.45),
            borderWeak: adjustLightness(hue, 0.20, isLight ? 0.70 : 0.40),
            borderStrong: adjustLightness(hue, 0.25, isLight ? 0.50 : 0.35),
            primary: baseColor,
            primaryDark: adjustDarkness(baseColor, 0.20),
            primaryLight: adjustLightness(baseColor, 0.20),
            primaryLink: adjustHue(baseColor, 5),
            bgSuccess: adjustLightness(hue, 0.40, isLight ? 0.95 : 0.25),
            textSuccess: semanticText(hue, bgApp),
            bgDanger: adjustLightness(hue, 0.45, isLight ? 0.98 : 0.15),
            textDanger: findReadableHue(hue, 0.80, semanticBg(hue), !isLight),
            menuBg: adjustDarkness(baseColor, 0.40),
            menuItem: adjustDarkness(baseColor, 0.50),
            menuHoverBg: adjustLightness(hue, 0.20, isLight ? 0.85 : 0.25),
            menuHoverText: semanticText(hue, bgApp),
            overlay: adjustDarkness(baseColor, 0.60),
            menuText: adjustLightness(baseColor, 0.80)
        };
    }

    var STORAGE_KEY = 'salas-theme';
    var available = (Array.isArray(window.__APP_THEMES__) && window.__APP_THEMES__.length)
        ? window.__APP_THEMES__.slice()
        : ['light', 'dark'];

    function isAvailable(theme) {
        return available.indexOf(theme) !== -1;
    }

    function sanitize(theme) {
        return isAvailable(theme) ? theme : null;
    }

    function getStored() {
        try {
            return localStorage.getItem(STORAGE_KEY);
        } catch (e) {
            return null;
        }
    }

    function setStored(theme) {
        try {
            localStorage.setItem(STORAGE_KEY, theme);
        } catch (e) {
            // ignore storage errors
        }
    }

    function getSystem() {
        try {
            return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
                ? 'dark'
                : 'light';
        } catch (e) {
            return 'light';
        }
    }

    // URL parameter takes precedence and updates the stored preference.
    var params;
    try {
        params = new URLSearchParams(window.location.search);
    } catch (e) {
        params = null;
    }
    var urlTheme = params ? sanitize(params.get('theme')) : null;
    if (urlTheme) {
        setStored(urlTheme);
    }

    var stored = sanitize(getStored());
    var preferred = stored || sanitize(getSystem()) || 'light';
    if (!isAvailable(preferred)) {
        preferred = available[0] || 'light';
    }

    var current = window.__APP_THEME__ || null;

    function redirectTo(theme) {
        try {
            var url = new URL(window.location.href);
            url.searchParams.set('theme', theme);
            window.location.replace(url.toString());
        } catch (e) {
            // fallback: hard reload with query string
            window.location.href = window.location.pathname + '?theme=' + encodeURIComponent(theme);
        }
    }

    // Apply preferred theme on first load if it differs from the server-rendered one.
    if (preferred && current && preferred !== current) {
        redirectTo(preferred);
    }

    // React to OS theme changes only when the user has not made an explicit choice.
    try {
        if (window.matchMedia) {
            var mq = window.matchMedia('(prefers-color-scheme: dark)');
            var handler = function () {
                if (getStored()) {
                    return; // explicit user choice wins
                }
                var sys = mq.matches ? 'dark' : 'light';
                if (isAvailable(sys) && sys !== (window.__APP_THEME__ || null)) {
                    redirectTo(sys);
                }
            };
            if (mq.addEventListener) {
                mq.addEventListener('change', handler);
            } else if (mq.addListener) {
                mq.addListener(handler);
            }
        }
    } catch (e) {
        // ignore
    }

    function setTheme(theme) {
        theme = sanitize(theme);
        if (!theme) {
            return;
        }
        setStored(theme);
        redirectTo(theme);
    }

    window.SalasTheme = {
        get: function () { return preferred; },
        set: setTheme,
        available: available.slice()
    };

    // Optional selector support: <select id="theme-selector">
    function bindSelector() {
        var sel = document.getElementById('theme-selector');
        if (!sel) {
            return;
        }
        if (!sel.options || sel.options.length === 0) {
            available.forEach(function (key) {
                var opt = document.createElement('option');
                opt.value = key;
                opt.textContent = key.charAt(0).toUpperCase() + key.slice(1);
                sel.appendChild(opt);
            });
        }
        try {
            sel.value = current || preferred;
        } catch (e) {
            // ignore
        }
        sel.addEventListener('change', function () {
            setTheme(sel.value);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindSelector);
    } else {
        bindSelector();
    }
})();
