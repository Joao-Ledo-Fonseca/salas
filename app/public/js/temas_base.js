(function () {
  'use strict';

  const themeState = {
    initialThemeTokens: {}
  };

  let paletteDebounceTimer = null;

  function initColoris() {
    Coloris({
      el: 'input.js-color-token',
      theme: 'default',
      themeMode: 'light',
      alpha: false,
      format: 'hex',
      swatches: [
        '#264653', '#2a9d8f', '#e9c46a', '#f4a261', '#e76f51',
        '#003049', '#d62828', '#f77f00', '#fcbf49', '#eae2b7',
        '#555b6e', '#89b0ae', '#bee3db', '#faf9f9', '#ffd6ba',
        '#0b132b', '#1c2541', '#3a506b', '#5bc0be', '#6fffe9'
      ],
      onChange: handleColorChange
    });

    // Blindar o popup #clr-picker criado pelo Coloris
    const colorisPopup = document.getElementById('clr-picker');
    if (colorisPopup) {
      colorisPopup.style.zIndex = '999999';
      // Usar fase de bubbling (false) para os controlos internos funcionarem
      ['mousedown', 'pointerdown', 'click', 'focusin'].forEach(event => {
        colorisPopup.addEventListener(event, e => e.stopPropagation(), false);
      });
    }
  }

  function handleColorChange(color, input) {
    if (!input) return;

    const cssVar = input.getAttribute('data-var');
    if (cssVar) {
      document.documentElement.style.setProperty(cssVar, color);
    }

    if (input.name === 'baseColor') {
      updatePaletteFromBase(color);
    }
  }

  function syncInputVisual(input, color) {
    input.value = color;
    const cssVar = input.getAttribute('data-var');
    if (cssVar) {
      document.documentElement.style.setProperty(cssVar, color);
    }
    // Atualiza a amostra visual nativa do Coloris sem disparar o onChange
    input.dispatchEvent(new Event('input', { bubbles: true }));
  }

  function updatePaletteFromBase(baseColor) {
    const form = document.forms['form_tema'];
    if (!form) return;

    clearTimeout(paletteDebounceTimer);
    paletteDebounceTimer = setTimeout(() => {
      fetch(`?acao=temas&ajax=calculate_palette&baseColor=${encodeURIComponent(baseColor)}`)
        .then(response => response.json())
        .then(palette => {
          if (!palette || typeof palette !== 'object') return;

          Object.keys(palette).forEach(key => {
            const hex = palette[key];
            // Procura por name="colors[key]", name="dashboard[key]" ou data-var="key"
            const input =
              form.querySelector(`[name="colors[${key}]"]`) ||
              form.querySelector(`[name="dashboard[${key}]"]`) ||
              form.querySelector(`[data-var="${key}"]`);

            if (input && typeof hex === 'string') {
              syncInputVisual(input, hex);
            } else if (key.startsWith('--') && typeof hex === 'string') {
              document.documentElement.style.setProperty(key, hex);
            }
          });
        })
        .catch(console.error);
    }, 150);
  }

  window.aplicarPreview = function () {
    const form = document.forms['form_tema'];
    if (!form) return;

    const fontSelect = form.querySelector('select[name="fontFamily"]');
    if (fontSelect) {
      const stack = fontSelect.options[fontSelect.selectedIndex]?.getAttribute('data-stack');
      if (stack) {
        document.documentElement.style.setProperty('--font-family-base', stack);
      }
    }

    const fontSizeInput = form.querySelector('input[name="fontSizeBase"]');
    if (fontSizeInput && fontSizeInput.value) {
      document.documentElement.style.setProperty('--font-size-base', fontSizeInput.value + 'px');
    }
  };

  window.reporTema = function () {
    const form = document.forms['form_tema'];
    if (!form) return;

    Object.keys(themeState.initialThemeTokens).forEach(name => {
      const input = form.querySelector(`[name="${name}"]`);
      if (input) {
        syncInputVisual(input, themeState.initialThemeTokens[name]);
      }
    });
    aplicarPreview();
  };

  document.addEventListener('DOMContentLoaded', function () {
    const form = document.forms['form_tema'];
    if (form) {
      form.querySelectorAll('.js-color-token').forEach(input => {
        themeState.initialThemeTokens[input.name] = input.value;
      });
    }

    initColoris();
  });
})();
