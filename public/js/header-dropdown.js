/**
 * Dropdowns premium du header — positionnement maîtrisé (CSS + JS custom)
 * ---------------------------------------------------------------
 * - Ouvre / ferme les menus au clic (aucune dépendance à Bootstrap Popper,
 *   dont les styles inline cassent la position, surtout sur mobile).
 * - Positionnement 100 % CSS : sous le toggle sur desktop, overlay sous le
 *   header sur mobile (z-index élevé, sans impact sur la taille du header).
 * - Fermeture : clic extérieur, Échap, scroll, redimensionnement, clic sur un item.
 */
(function () {
    'use strict';

    function allMenus() {
        return Array.prototype.slice.call(document.querySelectorAll('.app-nav .dropdown-menu'));
    }

    function isOpen(menu) {
        return menu.classList.contains('show');
    }

    function toggleOf(menu) {
        return menu.previousElementSibling;
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

    function init() {
        var toggles = Array.prototype.slice.call(
            document.querySelectorAll('.app-nav .dropdown-toggle, .app-nav .app-user')
        );
        toggles.forEach(bindToggle);

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
