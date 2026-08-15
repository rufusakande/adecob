/**
 * Plateforme — Ajustements UI mobile
 * - Ferme le menu burger après un clic sur un lien.
 * - Détecte la barre d'onglets mobile (.adc-tabbar).
 * NB : le padding-top du body est géré en CSS par le layout
 * (108px desktop / 64px mobile via media query).
 */
(function () {
    'use strict';

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
    }

    function markTabbar() {
        if (document.querySelector('.adc-tabbar')) {
            document.body.classList.add('has-tabbar');
        }
    }

    function init() {
        markTabbar();
        bindCollapse();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
