(function () {
  'use strict';

  var googleFontLoadId = 0;

  window.aplicarPreviewFontes = function (form) {
    form = form || document.forms['form_tema'];
    if (!form) return;

    var root = document.documentElement;
    var fontFamily = form.querySelector('select[name="fontFamily"]');
    var fontSize = form.querySelector('input[name="fontSizeBase"]');
    if (fontFamily && fontFamily.value) {
      var selectedFont = fontFamily.options[fontFamily.selectedIndex];
      var fontStack = selectedFont && selectedFont.getAttribute('data-font-stack');
      var fontUrl = selectedFont && selectedFont.getAttribute('data-font-url');
      if (fontStack) {
        root.style.setProperty('--font-family-base', fontStack);
      }
      updateGoogleFontLink(fontUrl, fontFamily.value);
    }
    if (fontSize && fontSize.value) {
      var size = fontSize.value;
      if (/^\d+$/.test(size)) size += 'px';
      root.style.setProperty('--font-size-base', size);
    }
  };

  function updateGoogleFontLink(url, family) {
    var link = document.getElementById('theme-google-font');
    if (!url) {
      googleFontLoadId++;
      if (link) link.remove();
      return;
    }
    if (!link) {
      link = document.createElement('link');
      link.id = 'theme-google-font';
      link.rel = 'stylesheet';
      document.head.appendChild(link);
    }

    var loadId = ++googleFontLoadId;
    var stylesheetLoaded = link.href === url && (link.dataset.loaded === 'true' || link.sheet)
      ? Promise.resolve()
      : new Promise(function (resolve, reject) {
        link.addEventListener('load', function () {
          link.dataset.loaded = 'true';
          resolve();
        }, { once: true });
        link.addEventListener('error', function () {
          link.dataset.loaded = 'false';
          reject(new Error('Não foi possível carregar a folha da Google Font selecionada.'));
        }, { once: true });
        if (link.href !== url) {
          link.dataset.loaded = 'false';
          link.href = url;
        }
      });

    stylesheetLoaded
      .then(function () {
        if (loadId !== googleFontLoadId || !document.fonts) return;
        return document.fonts.load('400 16px "' + family.replace(/"/g, '\\"') + '"');
      })
      .catch(function (error) {
        if (loadId === googleFontLoadId) {
          console.error(error);
        }
      });
  }
})();
