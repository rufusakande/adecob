/**
 * Reconstruit un objet File à partir d'un objet Base64 stocké
 */
function base64ToFile(base64Obj) {
    const arr = base64Obj.data.split(',');
    const bstr = atob(arr[1]);
    let n = bstr.length;
    const u8arr = new Uint8Array(n);
    while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new File([u8arr], base64Obj.name, { type: base64Obj.type });
}

/**
 * Tente de synchroniser toutes les infrastructures en attente
 */
async function syncOfflineInfrastructures() {
    if (!navigator.onLine) {
        console.log("Impossible de synchroniser : hors-ligne.");
        return;
    }

    try {
        const keys = await localforage.keys();
        if (keys.length === 0) return; // Rien à synchroniser

        console.log(`${keys.length} infrastructure(s) à synchroniser...`);
        
        // On récupère le token CSRF de la page actuelle
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // On peut afficher un loader global ici
        if (typeof showCustomLoader === 'function') {
            showCustomLoader('Synchronisation des données...', `${keys.length} élément(s) en attente`);
        }

        for (const key of keys) {
            const infraData = await localforage.getItem(key);
            if (!infraData) continue;

            // Reconstruire le FormData
            const formData = new FormData();
            
            // Ajouter les champs texte
            for (const [fieldName, value] of Object.entries(infraData.fields)) {
                if (Array.isArray(value)) {
                    value.forEach(v => formData.append(fieldName, v));
                } else {
                    formData.append(fieldName, value);
                }
            }

            // Ajouter les fichiers reconstruits
            for (const [fieldName, fileObj] of Object.entries(infraData.files)) {
                formData.append(fieldName, base64ToFile(fileObj));
            }

            // Envoyer au serveur (on suppose que l'URL d'action est la route 'store' classique)
            // On peut récupérer cette URL via une balise meta ou la coder en dur (moins idéal)
            // Ici on va cibler l'API de création. Normalement, c'est /infrastructures/create ou /infrastructures
            // On va utiliser la route de base de la page, ou un meta tag.
            const syncUrl = window.AppConfig?.storeUrl || '/infrastructures';

            try {
                const response = await fetch(syncUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (response.ok || response.status === 201 || response.status === 302) {
                    // Succès ! On supprime l'entrée locale
                    await localforage.removeItem(key);
                    console.log(`Infrastructure ${key} synchronisée avec succès.`);
                } else {
                    console.error(`Erreur serveur pour l'infra ${key}:`, response.status);
                    // Si on a une erreur 419 (CSRF invalide), il faudra peut-être recharger la page.
                    if (response.status === 419) {
                        alert("Votre session a expiré. La page va se recharger.");
                        window.location.reload();
                        return; // Arrêter la boucle
                    }
                }
            } catch (fetchErr) {
                console.error(`Impossible de joindre le serveur pour ${key}:`, fetchErr);
                // On arrête la boucle si le serveur est inaccessible
                break;
            }
        }

        // Mettre à jour le badge et cacher le loader
        if (typeof updateSyncBadge === 'function') updateSyncBadge();
        if (typeof hideCustomLoader === 'function') hideCustomLoader();

        // Notifier l'utilisateur
        const remainingKeys = await localforage.keys();
        if (remainingKeys.length === 0) {
            alert('Toutes les infrastructures en attente ont été synchronisées !');
            // Recharger la page si on est sur l'index pour voir les nouvelles données
            if (window.location.pathname === '/infrastructures') {
                window.location.reload();
            }
        }

    } catch (err) {
        console.error('Erreur lors de la synchronisation:', err);
        if (typeof hideCustomLoader === 'function') hideCustomLoader();
    }
}

// Écouter le retour de la connexion
window.addEventListener('online', () => {
    // Petit délai pour laisser la connexion se stabiliser
    setTimeout(syncOfflineInfrastructures, 3000);
});

// Écouter le clic sur le bouton de sync manuelle
document.addEventListener('DOMContentLoaded', () => {
    const syncBtn = document.getElementById('offline-sync-badge');
    if (syncBtn) {
        syncBtn.addEventListener('click', (e) => {
            e.preventDefault();
            syncOfflineInfrastructures();
        });
    }
});
