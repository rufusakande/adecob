/**
 * ADECOB — Ajustements UI mobile
 * - Calcule dynamiquement la hauteur de l'en-tête fixe (header + navbar)
 *   afin que le contenu ne soit jamais masqué, quelle que soit la taille d'écran.
 * - Repositionne la navbar sous le header.
 * - Ferme le menu burger après un clic sur un lien.
 */
(function () {
    'use strict';

    var header = document.querySelector('.fixed-header');
    var navbar = document.querySelector('.fixed-navbar');
    var root = document.documentElement;

    function syncHeader() {
        if (!header || !navbar) return;

        var headerH = header.offsetHeight;
        navbar.style.top = headerH + 'px';

        var total = headerH + navbar.offsetHeight + 12;
        root.style.setProperty('--adc-header-h', total + 'px');
    }

    function bindCollapse() {
        var collapseEl = document.getElementById('navbarNav');
        if (!collapseEl || !window.bootstrap) return;

        collapseEl.addEventListener('click', function (e) {
            var link = e.target.closest('a.nav-link:not(.dropdown-toggle), .dropdown-item');
            if (!link) return;
            if (window.innerWidth >= 992) return;
            var inst = window.bootstrap.Collapse.getInstance(collapseEl);
            if (inst) inst.hide();
        });

        ['shown.bs.collapse', 'hidden.bs.collapse'].forEach(function (evt) {
            collapseEl.addEventListener(evt, syncHeader);
        });
    }

    function markTabbar() {
        if (document.querySelector('.adc-tabbar')) {
            document.body.classList.add('has-tabbar');
        }
    }

    function init() {
        markTabbar();
        syncHeader();
        bindCollapse();

        if (window.ResizeObserver && header && navbar) {
            var ro = new ResizeObserver(syncHeader);
            ro.observe(header);
            ro.observe(navbar);
        }
        window.addEventListener('resize', syncHeader, { passive: true });
        window.addEventListener('orientationchange', syncHeader);
        window.addEventListener('load', syncHeader);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
