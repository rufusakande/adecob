/* ============================================================
   UI Confirmation & Loader globaux
   - Remplace les alertes natives confirm() par une modale premium
   - Affiche un loader fluide pendant les opérations (POST/PUT/DELETE)
   Utilisation : <form class="js-confirm-submit"
                      data-confirm-title="..." data-confirm-message="..."
                      data-confirm-icon="danger|warning|info|success"
                      data-confirm-ok="Confirmer" data-loader-text="...">
   ============================================================ */
(function () {
    'use strict';

    var ICONS = {
        warning: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>',
        danger: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>',
        info: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        success: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
    };

    var loader   = document.getElementById('appLoader');
    var modal    = document.getElementById('appConfirmModal');
    var okBtn    = document.getElementById('appConfirmOk');
    var titleEl  = document.getElementById('appConfirmTitle');
    var msgEl    = document.getElementById('appConfirmMessage');
    var iconEl   = document.getElementById('appConfirmIcon');
    var pendingForm = null;
    var pendingCallback = null;
    var lastFocused = null;
    var loaderHideTimer = null;

    /* ---------- Loader ---------- */
    function showLoader(text) {
        if (!loader) return;
        if (text) {
            var t = loader.querySelector('.app-loader__text');
            if (t) t.textContent = text;
        }
        loader.classList.add('is-visible');
        loader.setAttribute('aria-hidden', 'false');
        // Sécurité : si la page ne navigue pas (ex: erreur serveur), masquer le loader
        // pour éviter un spinner infini.
        if (loaderHideTimer) clearTimeout(loaderHideTimer);
        loaderHideTimer = setTimeout(hideLoader, 12000);
    }
    function hideLoader() {
        if (loaderHideTimer) { clearTimeout(loaderHideTimer); loaderHideTimer = null; }
        if (!loader) return;
        loader.classList.remove('is-visible');
        loader.setAttribute('aria-hidden', 'true');
    }

    /* ---------- Modale ---------- */
    function openConfirm(opts) {
        if (!modal) return;
        if (titleEl) titleEl.textContent = opts.title || 'Confirmer l\'action';
        if (msgEl) msgEl.textContent = opts.message || 'Êtes-vous sûr de vouloir continuer ?';
        if (okBtn) okBtn.textContent = opts.okText || 'Confirmer';
        if (iconEl) {
            iconEl.className = 'app-modal__icon app-modal__icon--' + (opts.icon || 'warning');
            iconEl.innerHTML = ICONS[opts.icon] || ICONS.warning;
        }
        // Mémoriser l'élément déclencheur pour lui rendre le focus à la fermeture
        lastFocused = document.activeElement;
        pendingCallback = opts.onConfirm || null;
        modal.removeAttribute('inert');
        modal.classList.add('is-open');
        if (okBtn) okBtn.focus();
    }
    function closeConfirm() {
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.setAttribute('inert', '');
        pendingForm = null;
        pendingCallback = null;
        // Restituer le focus à l'élément déclencheur (évite de laisser le focus
        // sur un élément caché / aria-hidden et l'avertissement associé)
        if (lastFocused && typeof lastFocused.focus === 'function') {
            lastFocused.focus();
        }
        lastFocused = null;
    }

    function getFormMethod(form) {
        var method = (form.getAttribute('method') || 'GET').toUpperCase();
        var hidden = form.querySelector('input[name="_method"]');
        if (hidden && hidden.value) method = hidden.value.toUpperCase();
        return method;
    }

    /* Confirmation de la modale → soumettre le formulaire */
    if (okBtn) {
        okBtn.addEventListener('click', function () {
            // Cas 1 : formulaire avec modale de confirmation (js-confirm-submit)
            if (pendingForm) {
                var form = pendingForm;
                pendingForm = null;
                closeConfirm();
                showLoader(form.getAttribute('data-loader-text') || undefined);
                // Soumission programmatique : n'émet PAS l'événement submit (aucune boucle, une seule soumission)
                form.submit();
                return;
            }
            // Cas 2 : confirmation programmatique (window.adecobUI.confirm avec onConfirm)
            if (pendingCallback) {
                var cb = pendingCallback;
                pendingCallback = null;
                closeConfirm();
                cb();
            }
        });
    }

    /* Fermeture : bouton annuler, backdrop, touche Échap */
    document.addEventListener('click', function (e) {
        if (e.target && e.target.closest('[data-app-modal-close]')) {
            closeConfirm();
        }
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal && modal.classList.contains('is-open')) {
            closeConfirm();
        }
    });

    /* ---------- Interception des soumissions ---------- */
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || form.tagName !== 'FORM') return;

        // Formulaire avec confirmation (modale premium)
        if (form.matches('.js-confirm-submit') || form.hasAttribute('data-confirm')) {
            e.preventDefault();
            pendingForm = form;
            openConfirm({
                title: form.getAttribute('data-confirm-title'),
                message: form.getAttribute('data-confirm-message'),
                okText: form.getAttribute('data-confirm-ok'),
                icon: form.getAttribute('data-confirm-icon') || 'warning'
            });
            return;
        }

        // Loader automatique pour les opérations d'écriture (sans empêcher la soumission native)
        var method = getFormMethod(form);
        if (['POST', 'PUT', 'PATCH', 'DELETE'].indexOf(method) !== -1) {
            // Ne pas afficher le loader si la validation HTML native bloque la soumission
            if (form.checkValidity()) {
                showLoader(form.getAttribute('data-loader-text') || undefined);
            }
        }
    });

    /* Boutons d'action (liens de navigation) : afficher le loader global */
    document.addEventListener('click', function (e) {
        var trigger = e.target && e.target.closest ? e.target.closest('.action-loader-btn') : null;
        if (trigger) {
            showLoader(trigger.getAttribute('data-loader-text') || 'Chargement...');
        }
    });

    /* Modales Bootstrap : retirer le focus d'un élément caché à la fermeture
       (évite l'avertissement "aria-hidden on a focused element") */
    document.addEventListener('hidden.bs.modal', function (e) {
        var m = e.target;
        if (m && m.contains && m.contains(document.activeElement) && document.activeElement.blur) {
            document.activeElement.blur();
        }
    });

    /* API publique (pour usage programmatique) */
    window.adecobUI = {
        showLoader: showLoader,
        hideLoader: hideLoader,
        confirm: openConfirm
    };

    /* Si la page revient du cache (bfcache), masquer un éventuel loader resté affiché */
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) hideLoader();
    });
})();
