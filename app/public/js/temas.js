(function () {
  'use strict';

  function initPickers() {
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
        btn.style.marginLeft = '6px';
        var originalColor = input.value || '#000000';
        btn.style.background = originalColor;
        input.parentNode.insertBefore(btn, input.nextSibling);

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
        });
      } catch (e) {
        // ignore
      }
    });
  }

  window.aplicarPreview = function () {
    var root = document.documentElement;
    var form = document.forms['form_tema'];
    if (!form) return;
    var fontFamily = form.querySelector('input[name="fontFamily"]');
    var fontSize = form.querySelector('input[name="fontSizeBase"]');
    if (fontFamily && fontFamily.value) {
      root.style.setProperty('--font-family-base', fontFamily.value);
    }
    if (fontSize && fontSize.value) {
      var fs = fontSize.value;
      if (/^\d+$/.test(fs)) fs = fs + 'px';
      root.style.setProperty('--font-size-base', fs);
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

  document.addEventListener('DOMContentLoaded', function () {
    initPickers();
  });
})();
