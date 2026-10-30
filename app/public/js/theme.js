(function () {
    'use strict';

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
