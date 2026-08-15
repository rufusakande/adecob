/* Plateforme — Invitation à installer l'application (tous navigateurs / tous écrans) */
(function () {
    'use strict';

    var STORAGE_KEY = 'adecob_install_dismissed_at';
    var SNOOZE_MS = 3 * 24 * 60 * 60 * 1000; // 3 jours
    var deferredPrompt = null;
    var banner = null;
    var modal = null;

    // Nom de l'application — injecté par le layout via <meta name="app-name"> (variable .env)
    var APP_NAME = 'ARMANI';
    try {
        var metaApp = document.querySelector('meta[name="app-name"]');
        if (metaApp && metaApp.content) { APP_NAME = metaApp.content; }
    } catch (e) { /* meta indisponible : on garde le fallback */ }

    function isStandalone() {
        return (
            window.matchMedia('(display-mode: standalone)').matches ||
            window.matchMedia('(display-mode: fullscreen)').matches ||
            window.matchMedia('(display-mode: minimal-ui)').matches ||
            window.navigator.standalone === true ||
            document.referrer.startsWith('android-app://')
        );
    }

    function isSnoozed() {
        try {
            var at = parseInt(window.localStorage.getItem(STORAGE_KEY) || '0', 10);
            return at > 0 && Date.now() - at < SNOOZE_MS;
        } catch (e) {
            return false;
        }
    }

    function snooze() {
        try {
            window.localStorage.setItem(STORAGE_KEY, String(Date.now()));
        } catch (e) { /* stockage indisponible */ }
    }

    function detectPlatform() {
        var ua = navigator.userAgent;
        var isIOS = /iPad|iPhone|iPod/.test(ua) ||
            (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
        if (isIOS) {
            return /CriOS/.test(ua) ? 'ios-chrome' : /FxiOS/.test(ua) ? 'ios-firefox' : 'ios-safari';
        }
        if (/Android/.test(ua)) {
            return /Firefox/.test(ua) ? 'android-firefox' :
                /SamsungBrowser/.test(ua) ? 'android-samsung' : 'android-chrome';
        }
        if (/Firefox/.test(ua)) return 'desktop-firefox';
        if (/Safari/.test(ua) && !/Chrome|Chromium|Edg/.test(ua)) return 'desktop-safari';
        return 'desktop-chromium';
    }

    var INSTRUCTIONS = {
        'ios-safari': [
            'Appuyez sur le bouton <strong>Partager</strong> (carré avec une flèche) en bas de Safari.',
            'Faites défiler puis choisissez <strong>« Sur l’écran d’accueil »</strong>.',
            'Confirmez avec <strong>Ajouter</strong> : l’icône ' + APP_NAME + ' apparaît sur votre écran d’accueil.'
        ],
        'ios-chrome': [
            'Appuyez sur le bouton <strong>Partager</strong> dans la barre d’adresse.',
            'Choisissez <strong>« Sur l’écran d’accueil »</strong>.',
            'Confirmez avec <strong>Ajouter</strong>. (Astuce : Safari offre la meilleure installation sur iPhone.)'
        ],
        'ios-firefox': [
            'Ouvrez ce site dans <strong>Safari</strong> pour pouvoir l’installer.',
            'Appuyez sur <strong>Partager</strong> puis <strong>« Sur l’écran d’accueil »</strong>.'
        ],
        'android-chrome': [
            'Ouvrez le menu <strong>⋮</strong> en haut à droite de Chrome.',
            'Choisissez <strong>« Installer l’application »</strong> ou <strong>« Ajouter à l’écran d’accueil »</strong>.',
            'Confirmez avec <strong>Installer</strong>.'
        ],
        'android-samsung': [
            'Ouvrez le menu <strong>≡</strong> du navigateur Samsung Internet.',
            'Choisissez <strong>« Ajouter la page à »</strong> puis <strong>« Écran d’accueil »</strong>.'
        ],
        'android-firefox': [
            'Ouvrez le menu <strong>⋮</strong> de Firefox.',
            'Choisissez <strong>« Installer »</strong> ou <strong>« Ajouter à l’écran d’accueil »</strong>.'
        ],
        'desktop-chromium': [
            'Cliquez sur l’icône <strong>d’installation</strong> (écran avec une flèche) à droite de la barre d’adresse.',
            'Sinon : menu <strong>⋮</strong> → <strong>« Installer ' + APP_NAME + '… »</strong>.',
            'Confirmez avec <strong>Installer</strong>.'
        ],
        'desktop-firefox': [
            'Firefox n’installe pas encore les applications web sur ordinateur.',
            'Créez un raccourci : <strong>Ctrl + D</strong> pour ajouter la plateforme à vos favoris,',
            'ou installez ' + APP_NAME + ' depuis Chrome, Edge ou votre téléphone.'
        ],
        'desktop-safari': [
            'Dans la barre de menus, ouvrez <strong>Fichier</strong>.',
            'Choisissez <strong>« Ajouter au Dock »</strong> (macOS Sonoma et versions ultérieures).'
        ]
    };

    function buildModal() {
        if (modal) return modal;
        var platform = detectPlatform();
        var steps = INSTRUCTIONS[platform] || INSTRUCTIONS['desktop-chromium'];

        modal = document.createElement('div');
        modal.className = 'adecob-install-modal';
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        modal.setAttribute('aria-label', 'Installer l’application ' + APP_NAME);

        var dialog = document.createElement('div');
        dialog.className = 'adecob-install-modal__dialog';

        var title = document.createElement('div');
        title.className = 'adecob-install-modal__title';
        title.textContent = 'Installer ' + APP_NAME + ' sur votre appareil';

        var intro = document.createElement('p');
        intro.className = 'adecob-install-modal__intro';
        intro.textContent = 'Suivez ces étapes pour ajouter la plateforme comme une application (accès rapide et mode hors-ligne).';

        var list = document.createElement('ol');
        list.className = 'adecob-install-steps';
        steps.forEach(function (step) {
            var li = document.createElement('li');
            li.innerHTML = step;
            list.appendChild(li);
        });

        var close = document.createElement('button');
        close.type = 'button';
        close.className = 'adecob-install-modal__close';
        close.textContent = 'J’ai compris';
        close.addEventListener('click', closeModal);

        dialog.appendChild(title);
        dialog.appendChild(intro);
        dialog.appendChild(list);
        dialog.appendChild(close);
        modal.appendChild(dialog);
        modal.addEventListener('click', function (event) {
            if (event.target === modal) closeModal();
        });
        document.body.appendChild(modal);
        return modal;
    }

    function openModal() {
        buildModal().classList.add('is-open');
    }

    function closeModal() {
        if (modal) modal.classList.remove('is-open');
    }

    function hideBanner() {
        if (!banner) return;
        banner.classList.remove('is-visible');
        window.setTimeout(function () {
            if (banner && banner.parentNode) banner.parentNode.removeChild(banner);
            banner = null;
        }, 300);
    }

    function onInstallClick() {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(function (choice) {
                if (choice && choice.outcome === 'accepted') {
                    hideBanner();
                } else {
                    snooze();
                    hideBanner();
                }
                deferredPrompt = null;
            });
            return;
        }
        openModal();
    }

    function showBanner() {
        if (banner || isStandalone() || isSnoozed()) return;

        banner = document.createElement('div');
        banner.className = 'adecob-install-banner';
        banner.setAttribute('role', 'region');
        banner.setAttribute('aria-label', 'Installer l’application ' + APP_NAME);

        var icon = document.createElement('img');
        icon.className = 'adecob-install-banner__icon';
        icon.src = '/icon-192x192.png';
        icon.alt = 'Logo ' + APP_NAME;
        icon.addEventListener('error', function () {
            icon.src = '/logo.jpg';
        });

        var text = document.createElement('div');
        text.className = 'adecob-install-banner__text';
        var title = document.createElement('div');
        title.className = 'adecob-install-banner__title';
        title.textContent = 'Installer l’application ' + APP_NAME;
        var desc = document.createElement('div');
        desc.className = 'adecob-install-banner__desc';
        desc.textContent = 'Accès rapide depuis votre écran d’accueil et saisie hors-ligne.';
        text.appendChild(title);
        text.appendChild(desc);

        var actions = document.createElement('div');
        actions.className = 'adecob-install-banner__actions';

        var install = document.createElement('button');
        install.type = 'button';
        install.className = 'adecob-install-btn';
        install.textContent = 'Installer';
        install.addEventListener('click', onInstallClick);

        var close = document.createElement('button');
        close.type = 'button';
        close.className = 'adecob-install-close';
        close.setAttribute('aria-label', 'Fermer');
        close.innerHTML = '&times;';
        close.addEventListener('click', function () {
            snooze();
            hideBanner();
        });

        actions.appendChild(install);
        actions.appendChild(close);

        banner.appendChild(icon);
        banner.appendChild(text);
        banner.appendChild(actions);
        document.body.appendChild(banner);

        window.requestAnimationFrame(function () {
            banner.classList.add('is-visible');
        });
    }

    window.addEventListener('beforeinstallprompt', function (event) {
        event.preventDefault();
        deferredPrompt = event;
        showBanner();
    });

    window.addEventListener('appinstalled', function () {
        snooze();
        hideBanner();
        closeModal();
    });

    // Point d'entrée manuel (ex. bouton « Installer » dans un menu)
    window.adecobShowInstallPrompt = function () {
        try { window.localStorage.removeItem(STORAGE_KEY); } catch (e) { /* noop */ }
        if (isStandalone()) return;
        if (deferredPrompt) { onInstallClick(); return; }
        openModal();
    };

    function boot() {
        if (isStandalone()) return;
        if (document.querySelector('.adc-tabbar')) {
            document.body.classList.add('has-mobile-tabbar');
        }
        // Affiche l'invitation même sans `beforeinstallprompt`
        // (iOS Safari, Firefox, Samsung Internet…), avec instructions manuelles.
        window.setTimeout(showBanner, 2500);
    }

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        boot();
    } else {
        document.addEventListener('DOMContentLoaded', boot);
    }
})();
