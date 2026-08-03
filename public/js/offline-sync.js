// Gestion de la file d'attente hors-ligne (localForage) + synchronisation serveur.
const OFFLINE_STORE_KEY = 'pending_infrastructures';

async function getOfflineQueue() {
    try {
        return (await localforage.getItem(OFFLINE_STORE_KEY)) || [];
    } catch (e) {
        return [];
    }
}

async function setOfflineQueue(items) {
    await localforage.setItem(OFFLINE_STORE_KEY, items);
}

async function deleteOfflineItem(localId) {
    const items = await getOfflineQueue();
    await setOfflineQueue(items.filter((i) => i.local_id !== localId));
}

async function updateOfflineItem(localId, patch) {
    const items = await getOfflineQueue();
    const next = items.map((i) => (i.local_id === localId ? Object.assign({}, i, patch) : i));
    await setOfflineQueue(next);
}

function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) return meta.getAttribute('content');
    const input = document.querySelector('input[name="_token"]');
    return input ? input.value : '';
}

// Envoie une seule fiche au serveur. Retourne { ok, message }
async function pushOfflineItem(data) {
    try {
        const formData = new FormData();
        formData.append('_token', getCsrfToken());

        const photosBase64 = [];
        for (const [key, value] of Object.entries(data)) {
            if (key === 'local_id' || key === 'timestamp' || key === '_token') continue;
            if (key.startsWith('photo') && typeof value === 'string' && value.startsWith('data:image')) {
                photosBase64.push(value);
            } else if (Array.isArray(value)) {
                value.forEach((v) => formData.append(key + '[]', v));
            } else if (value !== null && value !== undefined) {
                formData.append(key, value);
            }
        }
        if (photosBase64.length > 0) {
            formData.append('photos_data', JSON.stringify(photosBase64));
        }

        const response = await fetch('/infrastructures', {
            method: 'POST',
            body: formData,
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });

        const redirectedToLogin = response.redirected && new URL(response.url).pathname === '/login';
        if (redirectedToLogin || response.status === 401 || response.status === 419) {
            return { ok: false, message: 'Votre session a expiré. Reconnectez-vous avant de synchroniser.' };
        }

        if (response.ok) {
            return { ok: true };
        }

        let message = 'Erreur serveur (' + response.status + ')';
        try {
            const payload = await response.json();
            if (payload && payload.errors) {
                message = Object.values(payload.errors).flat().join(' • ');
            } else if (payload && payload.message) {
                message = payload.message;
            }
        } catch (e) {}
        return { ok: false, message: message };
    } catch (e) {
        return { ok: false, message: 'Réseau indisponible' };
    }
}

// Synchronise une fiche précise (par local_id)
async function syncOfflineItem(localId) {
    const items = await getOfflineQueue();
    const item = items.find((i) => i.local_id === localId);
    if (!item) return { ok: false, message: 'Fiche introuvable' };

    const result = await pushOfflineItem(item);
    if (result.ok) {
        await deleteOfflineItem(localId);
    } else {
        await updateOfflineItem(localId, { last_error: result.message, last_try: Date.now() });
    }
    return result;
}

// Synchronise toute la file. Retourne { success, failed }
async function syncOfflineData() {
    const pendingData = await getOfflineQueue();
    if (pendingData.length === 0) return { success: 0, failed: 0 };

    let successCount = 0;
    const remainingData = [];

    for (const data of pendingData) {
        const result = await pushOfflineItem(data);
        if (result.ok) {
            successCount++;
        } else {
            remainingData.push(Object.assign({}, data, { last_error: result.message, last_try: Date.now() }));
        }
    }

    await setOfflineQueue(remainingData);
    return { success: successCount, failed: remainingData.length };
}

// Badge de navigation : redirige vers la page de gestion des fiches hors-ligne.
document.addEventListener('DOMContentLoaded', async function () {
    const pendingData = await getOfflineQueue();
    if (pendingData.length === 0) return;

    const navbar = document.querySelector('.navbar-nav');
    if (!navbar) return;

    const manageUrl = (window.OFFLINE_MANAGE_URL || '/infrastructures-hors-ligne');
    const syncLi = document.createElement('li');
    syncLi.className = 'nav-item ms-2';
    syncLi.innerHTML =
        '<a href="' + manageUrl + '" class="nav-link btn btn-warning text-dark px-3 py-1 fw-bold" id="offline-sync-badge" title="Gérer les fiches enregistrées hors-ligne">' +
        '<i class="bi bi-cloud-arrow-up-fill me-1"></i> <span class="count">' + pendingData.length + '</span> hors-ligne</a>';
    navbar.appendChild(syncLi);
});

window.addEventListener('offline', function () {
    document.documentElement.dataset.networkStatus = 'offline';
});

window.addEventListener('online', function () {
    document.documentElement.dataset.networkStatus = 'online';
});
