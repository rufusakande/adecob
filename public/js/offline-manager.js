// Page de gestion des fiches enregistrées hors-ligne.
(function () {
    const LABELS = {
        nom_infrastructure: 'Nom de l’infrastructure',
        commune: 'Commune',
        village: 'Village / Quartier',
        arrondissement: 'Arrondissement',
        secteur_domaine: 'Secteur',
        type_infrastructure: 'Type',
        etat_fonctionnement: 'État de fonctionnement',
        niveau_degradation: 'Niveau de dégradation',
        materiaux_construction: 'Matériaux',
        annee_construction: 'Année de construction',
        latitude: 'Latitude',
        longitude: 'Longitude',
        altitude: 'Altitude',
        precision_gps: 'Précision GPS',
        numero_telephone: 'Téléphone',
        nom_enqueteur: 'Enquêteur',
        rehabilitation: 'Réhabilitation',
        observations: 'Observations',
    };

    const HIDDEN_KEYS = ['_token', '_method', 'local_id', 'timestamp', 'last_error', 'last_try'];

    const escapeHtml = (v) =>
        String(v ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    const label = (k) => LABELS[k] || k.replace(/_/g, ' ');
    const isPhoto = (k, v) => typeof v === 'string' && v.startsWith('data:image');

    const fmtDate = (ts) => (ts ? new Date(ts).toLocaleString('fr-FR') : '—');

    let listEl, emptyEl, countEl;

    async function render() {
        const items = await getOfflineQueue();
        countEl.textContent = items.length;
        document.getElementById('offline-sync-all').disabled = items.length === 0;

        if (items.length === 0) {
            listEl.innerHTML = '';
            emptyEl.classList.remove('d-none');
            return;
        }
        emptyEl.classList.add('d-none');

        listEl.innerHTML = items
            .map((item) => {
                const photos = Object.entries(item).filter(([k, v]) => isPhoto(k, v)).length;
                return `
                <tr>
                    <td><strong>${escapeHtml(item.nom_infrastructure || 'Sans nom')}</strong><br>
                        <small class="text-muted">${escapeHtml(item.secteur_domaine || '—')} · ${escapeHtml(item.type_infrastructure || '—')}</small></td>
                    <td>${escapeHtml(item.commune || '—')}${item.village ? '<br><small class="text-muted">' + escapeHtml(item.village) + '</small>' : ''}</td>
                    <td>${fmtDate(item.timestamp)}</td>
                    <td>${photos > 0 ? '<span class="badge bg-secondary">' + photos + ' photo(s)</span>' : '<span class="text-muted">—</span>'}
                        ${item.last_error ? '<div class="small text-danger mt-1"><i class="fas fa-triangle-exclamation me-1"></i>' + escapeHtml(item.last_error) + '</div>' : ''}</td>
                    <td class="text-nowrap">
                        <div class="d-flex flex-wrap gap-1">
                            <button class="btn btn-sm btn-info text-white" data-action="view" data-id="${escapeHtml(item.local_id)}"><i class="fas fa-eye me-1"></i>Voir</button>
                            <button class="btn btn-sm btn-outline-primary" data-action="edit" data-id="${escapeHtml(item.local_id)}"><i class="fas fa-pen me-1"></i>Modifier</button>
                            <button class="btn btn-sm btn-success" data-action="sync" data-id="${escapeHtml(item.local_id)}"><i class="fas fa-cloud-arrow-up me-1"></i>Synchroniser</button>
                            <button class="btn btn-sm btn-outline-danger" data-action="delete" data-id="${escapeHtml(item.local_id)}"><i class="fas fa-trash me-1"></i>Supprimer</button>
                        </div>
                    </td>
                </tr>`;
            })
            .join('');
    }

    async function findItem(id) {
        return (await getOfflineQueue()).find((i) => i.local_id === id);
    }

    function showModal(title, bodyHtml, footerHtml) {
        document.getElementById('offlineModalTitle').textContent = title;
        document.getElementById('offlineModalBody').innerHTML = bodyHtml;
        document.getElementById('offlineModalFooter').innerHTML =
            footerHtml || '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>';
        bootstrap.Modal.getOrCreateInstance(document.getElementById('offlineModal')).show();
    }

    function hideModal() {
        bootstrap.Modal.getOrCreateInstance(document.getElementById('offlineModal')).hide();
    }

    async function viewItem(id) {
        const item = await findItem(id);
        if (!item) return;
        const rows = Object.entries(item)
            .filter(([k, v]) => !HIDDEN_KEYS.includes(k) && !isPhoto(k, v) && v !== '')
            .map(([k, v]) => `<tr><th class="w-40 text-muted small">${escapeHtml(label(k))}</th><td>${escapeHtml(v)}</td></tr>`)
            .join('');
        const photos = Object.entries(item)
            .filter(([k, v]) => isPhoto(k, v))
            .map(([, v]) => `<img src="${v}" class="img-thumbnail me-2 mb-2" style="max-height:140px">`)
            .join('');
        showModal(
            item.nom_infrastructure || 'Fiche hors-ligne',
            `<div class="table-responsive"><table class="table table-sm table-striped mb-3">${rows}</table></div>${photos ? '<div>' + photos + '</div>' : ''}`
        );
    }

    async function editItem(id) {
        const item = await findItem(id);
        if (!item) return;
        const fields = Object.entries(item)
            .filter(([k, v]) => !HIDDEN_KEYS.includes(k) && !isPhoto(k, v))
            .map(
                ([k, v]) => `
                <div class="col-md-6">
                    <label class="form-label small text-muted">${escapeHtml(label(k))}</label>
                    <input type="text" class="form-control form-control-sm" data-field="${escapeHtml(k)}" value="${escapeHtml(v)}">
                </div>`
            )
            .join('');
        showModal(
            'Modifier la fiche hors-ligne',
            `<form id="offline-edit-form" class="row g-3">${fields}</form>`,
            `<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
             <button type="button" class="btn btn-primary" id="offline-edit-save"><i class="fas fa-save me-1"></i>Enregistrer</button>`
        );

        document.getElementById('offline-edit-save').addEventListener('click', async function () {
            const patch = {};
            document.querySelectorAll('#offline-edit-form [data-field]').forEach((input) => {
                patch[input.dataset.field] = input.value;
            });
            patch.last_error = null;
            await updateOfflineItem(id, patch);
            hideModal();
            await render();
            notify('success', 'Fiche mise à jour sur cet appareil.');
        });
    }

    function notify(type, message) {
        const zone = document.getElementById('offline-alerts');
        zone.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show"><i class="fas fa-circle-info me-2"></i>${escapeHtml(message)}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
    }

    async function handleAction(action, id, btn) {
        if (action === 'view') return viewItem(id);
        if (action === 'edit') return editItem(id);

        if (action === 'delete') {
            if (!confirm('Supprimer définitivement cette fiche de cet appareil ? Elle ne sera pas envoyée au serveur.')) return;
            await deleteOfflineItem(id);
            await render();
            return notify('warning', 'Fiche supprimée de cet appareil.');
        }

        if (action === 'sync') {
            if (!navigator.onLine) return notify('danger', 'Vous devez être connecté à internet pour synchroniser.');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            const res = await syncOfflineItem(id);
            await render();
            notify(res.ok ? 'success' : 'danger', res.ok ? 'Fiche synchronisée et envoyée en validation.' : 'Échec : ' + res.message);
        }
    }

    document.addEventListener('DOMContentLoaded', async function () {
        listEl = document.getElementById('offline-list');
        emptyEl = document.getElementById('offline-empty');
        countEl = document.getElementById('offline-count');
        if (!listEl) return;

        await render();

        listEl.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-action]');
            if (!btn) return;
            handleAction(btn.dataset.action, btn.dataset.id, btn);
        });

        document.getElementById('offline-sync-all').addEventListener('click', async function () {
            if (!navigator.onLine) return notify('danger', 'Vous devez être connecté à internet pour synchroniser.');
            const items = await getOfflineQueue();
            if (!items.length || !confirm('Synchroniser ' + items.length + ' fiche(s) avec le serveur ?')) return;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Synchronisation...';
            const res = await syncOfflineData();
            this.innerHTML = '<i class="fas fa-cloud-arrow-up me-1"></i> Tout synchroniser';
            this.disabled = false;
            await render();
            notify(
                res.failed === 0 ? 'success' : 'warning',
                res.success + ' fiche(s) synchronisée(s)' + (res.failed ? ', ' + res.failed + ' en échec (voir le détail).' : '.')
            );
        });
    });
})();
