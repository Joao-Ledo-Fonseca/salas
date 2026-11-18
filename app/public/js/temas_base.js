(function () {
  'use strict';

  // Estado global do tema
  const themeState = {
    initialThemeTokens: {},
    currentThemeTokens: {}
  };

  // Inicialização do Coloris com blindagem
  function initColoris() {
    // Blindar o popup do Coloris
    const colorisPopup = document.createElement('div');
    colorisPopup.id = 'clr-picker';
    colorisPopup.style.zIndex = '999999';
    document.body.appendChild(colorisPopup);

    // Prevenir interferência das abas
    ['mousedown', 'pointerdown', 'click', 'focusin'].forEach(event => {
      colorisPopup.addEventListener(event, e => e.stopPropagation(), true);
    });

    // Configuração do Coloris
    Coloris({
      el: '.js-color-token',
      parent: '#clr-picker',
      theme: 'default',
      themeMode: 'light',
      alpha: false,
      format: 'hex',
      swatches: [
        '#264653','#2a9d8f','#e9c46a','#f4a261','#e76f51',
        '#003049','#d62828','#f77f00','#fcbf49','#eae2b7',
        '#555b6e','#89b0ae','#bee3db','#faf9f9','#ffd6ba',
        '#0b132b','#1c2541','#3a506b','#5bc0be','#6fffe9'
      ],
      onChange: handleColorChange
    });
  }

  // Handler para mudanças de cor
  function handleColorChange(color, input) {
    if (input.name === 'baseColor') {
      updatePaletteFromBase(color);
    } else {
      const cssVar = input.getAttribute('data-var');
      if (cssVar) {
        document.documentElement.style.setProperty(cssVar, color);
      }
    }
    updateColorPreview(input, color);
  }

  // Atualizar paleta a partir da cor base
  function updatePaletteFromBase(baseColor) {
    const form = document.forms['form_tema'];
    if (!form) return;

    // Simular requisição AJAX para cálculo da paleta
    fetch(`?acao=temas&ajax=calculate_palette&baseColor=${encodeURIComponent(baseColor)}`)
      .then(response => response.json())
      .then(palette => {
        if (!palette) return;

        // Aplicar novas cores aos inputs e ao :root
        Object.keys(palette).forEach(key => {
          const input = form.querySelector(`[name="colors[${key}]"]`);
          if (input) {
            input.value = palette[key];
            const cssVar = input.getAttribute('data-var');
            if (cssVar) {
              document.documentElement.style.setProperty(cssVar, palette[key]);
            }
            updateColorPreview(input, palette[key]);
          }
        });
      })
      .catch(console.error);
  }

  // Atualizar visualização da cor
  function updateColorPreview(input, color) {
    const cell = input.closest('td');
    if (cell) {
      const box = cell.querySelector('.color-preview-box');
      if (box) box.style.background = color;
    }
  }

  // Aplicar preview do tema
  window.aplicarPreview = function() {
    const form = document.forms['form_tema'];
    if (!form) return;

    // Aplicar fonte
    const fontSelect = form.querySelector('select[name="fontFamily"]');
    if (fontSelect) {
      const stack = fontSelect.options[fontSelect.selectedIndex]?.getAttribute('data-stack');
      if (stack) {
        document.documentElement.style.setProperty('--font-family-base', stack);
      }
    }

    // Aplicar tamanho da fonte
    const fontSizeInput = form.querySelector('input[name="fontSizeBase"]');
    if (fontSizeInput && fontSizeInput.value) {
      document.documentElement.style.setProperty('--font-size-base', fontSizeInput.value + 'px');
    }
  };

  // Repor tema inicial
  window.reporTema = function() {
    const form = document.forms['form_tema'];
    if (!form) return;

    // Restaurar valores iniciais
    Object.keys(themeState.initialThemeTokens).forEach(key => {
      const input = form.querySelector(`[name="${key}"]`);
      if (input) {
        input.value = themeState.initialThemeTokens[key];
        const cssVar = input.getAttribute('data-var');
        if (cssVar) {
          document.documentElement.style.setProperty(cssVar, themeState.initialThemeTokens[key]);
        }
        updateColorPreview(input, themeState.initialThemeTokens[key]);
      }
    });
    aplicarPreview();
  };

  // Inicialização
  document.addEventListener('DOMContentLoaded', function() {
    // Capturar estado inicial do tema
    const form = document.forms['form_tema'];
    if (form) {
      form.querySelectorAll('.js-color-token').forEach(input => {
        const name = input.name;
        const value = input.value;
        themeState.initialThemeTokens[name] = value;
        themeState.currentThemeTokens[name] = value;
      });
    }

    initColoris();
  });
})();
