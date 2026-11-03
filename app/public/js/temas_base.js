(function () {
  'use strict';

  function initPickers() {
    var baseColorInput = document.querySelector('input[name="baseColor"]');
    if (baseColorInput) {
      baseColorInput.addEventListener('change', function () {
        updatePaletteFromBase(baseColorInput.value);
      });
    }

    if (typeof Pickr === 'undefined') {
      return;
    }
    document.querySelectorAll('.color-input').forEach(function (input) {
      try {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'color-picker-btn';
        btn.style.width = '1.25rem';
        btn.style.height = '1.25rem';
        btn.style.border = '1px solid #ccc';
        btn.style.cursor = 'pointer';
        btn.style.verticalAlign = 'middle';
        btn.style.display = 'inline-block';
        btn.style.marginLeft = '6px';
        var originalColor = input.value || '#000000';
        btn.style.background = originalColor;
        var cell = input.closest('tr').querySelector('.col-btn'); if (cell) { cell.appendChild(btn); } else { input.parentNode.insertBefore(btn, input.nextSibling); }

        var pickr = Pickr.create({
          el: btn,
          useAsButton: true,
          theme: 'classic',
          default: originalColor,
          components: {
            preview: true,
            opacity: false,
            hue: true,
            interaction: { hex: true, input: true, save: true, cancel: true, clear: false }
          }
        });
        try { pickr.setColor(originalColor, true); } catch (e) { }
        pickr.on('cancel', function () {
          try { pickr.setColor(originalColor, true); } catch (e) { }
          input.value = originalColor;
          btn.style.background = originalColor;
          try { pickr.hide(); } catch (e) { }
        });
        pickr.on('save', function (color) {
          var hex = (color && typeof color.toHEXA === 'function') ? color.toHEXA().toString() : (input.value || originalColor);
          input.value = hex;
          btn.style.background = hex;
          originalColor = hex;
          try { pickr.hide(); } catch (e) { }
        });
        pickr.on('change', function (color) {
          var hex = color.toHEXA().toString();
          btn.style.background = hex;
          input.value = hex;
          if (input.name === 'baseColor') {
            updatePaletteFromBase(hex);
          }
        });
        input._themePickerUpdate = function (hex) {
          originalColor = hex;
          input.value = hex;
          btn.style.background = hex;
          pickr.setColor(hex, true);
        };
      } catch (e) {
        // ignore
      }
    });

  }

  function parseHexColor(value) {
    var hex = String(value || '').trim().replace(/^#/, '');
    if (/^[0-9a-f]{3}$/i.test(hex)) {
      hex = hex.split('').map(function (digit) { return digit + digit; }).join('');
    }
    if (!/^[0-9a-f]{6}$/i.test(hex)) return null;
    return {
      r: parseInt(hex.slice(0, 2), 16),
      g: parseInt(hex.slice(2, 4), 16),
      b: parseInt(hex.slice(4, 6), 16)
    };
  }

  function rgbToHex(rgb) {
    return '#' + [rgb.r, rgb.g, rgb.b].map(function (channel) {
      return Math.round(channel).toString(16).padStart(2, '0');
    }).join('').toUpperCase();
  }

  function rgbToHsl(rgb) {
    var r = rgb.r / 255;
    var g = rgb.g / 255;
    var b = rgb.b / 255;
    var max = Math.max(r, g, b);
    var min = Math.min(r, g, b);
    var h = 0;
    var s = 0;
    var l = (max + min) / 2;
    if (max !== min) {
      var delta = max - min;
      s = l > 0.5 ? delta / (2 - max - min) : delta / (max + min);
      if (max === r) h = (g - b) / delta + (g < b ? 6 : 0);
      else if (max === g) h = (b - r) / delta + 2;
      else h = (r - g) / delta + 4;
      h /= 6;
    }
    return { h: h * 360, s: s, l: l };
  }

  function hslToHex(h, s, l) {
    h = ((h % 360) + 360) % 360 / 360;
    var r;
    var g;
    var b;
    if (s === 0) {
      r = g = b = l;
    } else {
      var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
      var p = 2 * l - q;
      var hue = function (t) {
        if (t < 0) t += 1;
        if (t > 1) t -= 1;
        if (t < 1 / 6) return p + (q - p) * 6 * t;
        if (t < 1 / 2) return q;
        if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
        return p;
      };
      r = hue(h + 1 / 3);
      g = hue(h);
      b = hue(h - 1 / 3);
    }
    return rgbToHex({ r: r * 255, g: g * 255, b: b * 255 });
  }

  function relativeLuminance(hex) {
    var rgb = parseHexColor(hex);
    if (!rgb) return 0;
    var channels = [rgb.r, rgb.g, rgb.b].map(function (channel) {
      var value = channel / 255;
      return value <= 0.04045 ? value / 12.92 : Math.pow((value + 0.055) / 1.055, 2.4);
    });
    return 0.2126 * channels[0] + 0.7152 * channels[1] + 0.0722 * channels[2];
  }

  function contrastRatio(first, second) {
    var l1 = relativeLuminance(first);
    var l2 = relativeLuminance(second);
    return (Math.max(l1, l2) + 0.05) / (Math.min(l1, l2) + 0.05);
  }

  function contrastingText(background) {
    return contrastRatio(background, '#000000') >= contrastRatio(background, '#FFFFFF')
      ? '#171A1F'
      : '#F4F6F8';
  }

  function findReadableHue(hue, saturation, background, preferLight) {
    for (var step = 0; step <= 100; step++) {
      var lightness = preferLight ? 0.35 + step * 0.0065 : 0.65 - step * 0.0065;
      lightness = Math.max(0.02, Math.min(0.98, lightness));
      var candidate = hslToHex(hue, saturation, lightness);
      if (contrastRatio(candidate, background) >= 4.5) return candidate;
    }
    return contrastingText(background);
  }

  function updatePaletteFromBase(baseValue) {
    var baseInput = document.querySelector('input[name="baseColor"]');
    var baseRgb = parseHexColor(baseValue);
    if (!baseInput || !baseRgb) return;

    var base = rgbToHex(baseRgb);
    var hsl = rgbToHsl(baseRgb);
    var isLight = relativeLuminance(base) >= 0.179;
    var text = contrastingText(base);
    var surfaceHue = hsl.h;
    var surfaceSaturation = Math.min(hsl.s * 0.22, 0.12);
    var surface = hslToHex(surfaceHue, surfaceSaturation, isLight ? 0.96 : 0.17);
    var surfaceAlt = hslToHex(surfaceHue, surfaceSaturation, isLight ? 0.90 : 0.24);
    var border = hslToHex(surfaceHue, Math.max(surfaceSaturation, 0.04), isLight ? 0.78 : 0.38);
    var borderStrong = hslToHex(surfaceHue, Math.max(surfaceSaturation, 0.04), isLight ? 0.62 : 0.53);
    var primary = hslToHex(hsl.h, Math.max(hsl.s, 0.52), 0.34);
    while (contrastRatio('#FFFFFF', primary) < 4.5 && rgbToHsl(parseHexColor(primary)).l > 0.12) {
      primary = hslToHex(hsl.h, Math.max(hsl.s, 0.52), rgbToHsl(parseHexColor(primary)).l - 0.01);
    }
    var primaryDark = hslToHex(hsl.h, Math.max(hsl.s, 0.52), Math.max(0.1, rgbToHsl(parseHexColor(primary)).l - 0.08));
    var primaryLight = hslToHex(hsl.h, Math.max(hsl.s * 0.7, 0.25), 0.78);
    var primaryLink = findReadableHue(hsl.h, Math.max(hsl.s, 0.55), base, !isLight);
    var menuBg = hslToHex(hsl.h, Math.max(surfaceSaturation, 0.04), isLight ? 0.14 : 0.07);
    var menuItem = hslToHex(hsl.h, Math.max(surfaceSaturation, 0.04), isLight ? 0.23 : 0.14);
    var menuHoverBg = hslToHex(hsl.h, Math.max(surfaceSaturation, 0.04), isLight ? 0.78 : 0.30);
    var overlay = contrastingText(base) === '#F4F6F8' ? '#F4F6F8' : '#171A1F';
    var semantic = function (hue, lightness) {
      return hslToHex(hue, 0.42, lightness);
    };
    var semanticLightness = isLight ? 0.90 : 0.24;
    var dashboardAvailable = hslToHex(
      hsl.h,
      hsl.s,
      Math.min(1, hsl.l + 0.08)
    );

    var colors = {
      bg: base,
      surface: surface,
      surfaceAlt: surfaceAlt,
      text: text,
      textStrong: text,
      textMuted: findReadableHue(surfaceHue, 0.05, base, !isLight),
      border: border,
      borderStrong: borderStrong,
      primary: primary,
      primaryDark: primaryDark,
      primaryLight: primaryLight,
      primaryLink: primaryLink,
      success: semantic(135, semanticLightness),
      warning: semantic(45, semanticLightness),
      danger: semantic(355, semanticLightness),
      dangerDark: findReadableHue(355, 0.72, isLight ? '#F5DDDF' : '#3B2024', !isLight),
      dangerLight: hslToHex(355, 0.28, isLight ? 0.95 : 0.18),
      dangerBorder: semantic(355, isLight ? 0.70 : 0.42),
      menuBg: menuBg,
      menuItem: menuItem,
      menuHoverBg: menuHoverBg,
      menuHoverText: contrastingText(menuHoverBg),
      menuText: contrastingText(menuBg),
      overlay: overlay
    };
    var dashboard = {
      reservado: semantic(45, semanticLightness),
      confirmado: semantic(135, semanticLightness),
      cancelado: semantic(355, semanticLightness),
      disponivel: dashboardAvailable,
      disponivelHover: surfaceAlt,
      selected: semantic(135, semanticLightness),
      cellBorder: border,
      cellText: text
    };

    baseInput.value = base;
    if (typeof baseInput._themePickerUpdate === 'function') {
      baseInput._themePickerUpdate(base);
    }
    document.querySelectorAll('[data-palette-type][data-palette-key]').forEach(function (input) {
      var palette = input.getAttribute('data-palette-type') === 'dashboard' ? dashboard : colors;
      var key = input.getAttribute('data-palette-key');
      if (!Object.prototype.hasOwnProperty.call(palette, key)) return;
      var value = palette[key];
      if (input.value !== value) {
        if (typeof input._themePickerUpdate === 'function') {
          input._themePickerUpdate(value);
        } else {
          input.value = value;
        }
      }
    });
    window.aplicarPreview();
  }

  window.aplicarPreview = function () {
    var root = document.documentElement;
    var form = document.forms['form_tema'];
    if (!form) return;
    if (typeof window.aplicarPreviewFontes === 'function') {
      window.aplicarPreviewFontes(form);
    }
    document.querySelectorAll('.color-input').forEach(function (input) {
      var v = input.getAttribute('data-var');
      if (v && input.value) {
        root.style.setProperty(v, input.value);
      }
    });
  };

  window.reporTema = function () {
    window.location.reload();
  };

  window.initPickers = initPickers;
})();
