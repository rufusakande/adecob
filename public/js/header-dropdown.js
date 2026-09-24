/**
 * Dropdowns premium du header — positionnement maîtrisé (CSS + JS custom)
 * ---------------------------------------------------------------
 * - Ouvre / ferme les menus au clic (aucune dépendance à Bootstrap Popper,
 *   dont les styles inline cassent la position, surtout sur mobile).
 * - Positionnement 100 % CSS : sous le toggle sur desktop, overlay sous le
 *   header sur mobile (z-index élevé, sans impact sur la taille du header).
 * - Survol (souris) : « hover intent » — le menu survit au trajet de la souris
 *   vers le menu grâce à un délai de grâce (.is-open) + un pont CSS anti-écart.
 * - Fermeture : clic extérieur, Échap, scroll, redimensionnement, clic sur un item.
 */
(function () {
    'use strict';

    var HOVER_QUERY = '(hover: hover) and (min-width: 992px)';
    var CLOSE_DELAY = 300; // ms — laisse le temps d'atteindre le menu sans qu'il disparaisse

    function allMenus() {
        return Array.prototype.slice.call(document.querySelectorAll('.app-nav .dropdown-menu'));
    }

    function isOpen(menu) {
        return menu.classList.contains('show');
    }

    function toggleOf(menu) {
        return menu.previousElementSibling;
    }

    function navItemOf(menu) {
        return menu.closest ? menu.closest('.nav-item.dropdown') : null;
    }

    function setAria(menu, expanded) {
        var toggle = toggleOf(menu);
        if (toggle && toggle.hasAttribute) toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
    }

    function openMenu(menu, toggle) {
        allMenus().forEach(function (m) {
            m.classList.remove('show');
            setAria(m, false);
            var t = toggleOf(m);
            if (t && t.classList) t.classList.remove('active');
        });
        menu.classList.add('show');
        setAria(menu, true);
        if (toggle && toggle.classList) toggle.classList.add('active');
    }

    function closeMenu(menu) {
        menu.classList.remove('show');
        setAria(menu, false);
        var t = toggleOf(menu);
        if (t && t.classList) t.classList.remove('active');
        // Nettoyer aussi l'état de survol, sinon le menu resterait affiché via .is-open.
        var item = navItemOf(menu);
        if (item) item.classList.remove('is-open');
    }

    function closeAll() {
        allMenus().forEach(closeMenu);
    }

    function bindToggle(toggle) {
        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var menu = toggle.nextElementSibling;
            if (!menu || !menu.classList || !menu.classList.contains('dropdown-menu')) return;
            if (isOpen(menu)) closeMenu(menu);
            else openMenu(menu, toggle);
        });
    }

    /**
     * Le survol ne doit s'appliquer qu'aux appareils réellement équipés d'une souris
     * (sur mobile/tablette, le clic reste la seule ouverture). Le test est refait à
     * chaque événement pour rester correct après un redimensionnement de la fenêtre.
     */
    function hoverEnabled() {
        return !!(window.matchMedia && window.matchMedia(HOVER_QUERY).matches);
    }

    /**
     * Survol à la souris (desktop) : le menu reste affiché pendant CLOSE_DELAY après
     * la sortie du curseur, ce qui permet de rejoindre le menu même si le trajet n'est
     * pas rectiligne. Le pont CSS (.dropdown-menu::before) comble l'écart vertical
     * entre le lien et le menu.
     */
    function bindHoverIntent(item) {
        if (!item.querySelector(':scope > .dropdown-menu')) return;
        var timer = null;

        function cancel() {
            if (timer) { clearTimeout(timer); timer = null; }
        }

        function scheduleClose() {
            cancel();
            timer = setTimeout(function () {
                item.classList.remove('is-open');
                timer = null;
            }, CLOSE_DELAY);
        }

        item.addEventListener('mouseenter', function () {
            if (!hoverEnabled()) return;
            cancel();
            item.classList.add('is-open');
        });

        item.addEventListener('mouseleave', function () {
            if (!hoverEnabled()) return;
            scheduleClose();
        });

        // Navigation au clavier : même confort (Tab depuis le lien vers le menu).
        item.addEventListener('focusin', function () {
            cancel();
            item.classList.add('is-open');
        });
        item.addEventListener('focusout', scheduleClose);
    }

    function init() {
        var toggles = Array.prototype.slice.call(
            document.querySelectorAll('.app-nav .dropdown-toggle, .app-nav .app-user')
        );
        toggles.forEach(bindToggle);

        Array.prototype.slice.call(document.querySelectorAll('.app-nav .nav-item.dropdown'))
            .forEach(bindHoverIntent);

        // Clic extérieur : fermer tout
        document.addEventListener('click', function (e) {
            if (e.target.closest && e.target.closest('.app-nav .nav-item.dropdown')) return;
            closeAll();
        });

        // Clic sur un item du menu : fermer
        document.addEventListener('click', function (e) {
            if (e.target.closest && e.target.closest('.app-nav .dropdown-item')) closeAll();
        });

        // Échap
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeAll();
        });

        // Scroll / resize : fermer (sécurité)
        ['resize', 'scroll'].forEach(function (ev) {
            window.addEventListener(ev, closeAll, { passive: true });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
