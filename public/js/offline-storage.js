// Initialisation de la base de données locale
localforage.config({
    name: 'AdecobOfflineDB',
    storeName: 'infrastructures'
});

/**
 * Convertit un fichier (File/Blob) en Base64 pour le stocker facilement.
 */
function fileToBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve({
            name: file.name,
            type: file.type,
            data: reader.result
        });
        reader.onerror = error => reject(error);
    });
}

/**
 * Sauvegarde les données du formulaire localement
 */
async function saveInfrastructureLocally(formElement) {
    const formData = new FormData(formElement);
    const infraData = {
        id: Date.now().toString(), // ID unique temporaire
        savedAt: new Date().toISOString(),
        fields: {},
        files: {}
    };

    // Parcourir toutes les entrées du formulaire
    for (let [key, value] of formData.entries()) {
        if (value instanceof File && value.size > 0) {
            // C'est un fichier, on le convertit en Base64
            infraData.files[key] = await fileToBase64(value);
        } else if (!(value instanceof File)) {
            // Champ texte classique
            // Gérer les champs multiples (tableaux comme nom[])
            if (infraData.fields[key]) {
                if (!Array.isArray(infraData.fields[key])) {
                    infraData.fields[key] = [infraData.fields[key]];
                }
                infraData.fields[key].push(value);
            } else {
                infraData.fields[key] = value;
            }
        }
    }

    // Enregistrer dans IndexedDB
    try {
        await localforage.setItem(infraData.id, infraData);
        
        // Mettre à jour le compteur d'UI
        updateSyncBadge();

        return true;
    } catch (err) {
        console.error('Erreur lors de la sauvegarde locale:', err);
        return false;
    }
}

/**
 * Met à jour le badge affichant le nombre d'infrastructures en attente
 */
async function updateSyncBadge() {
    try {
        const keys = await localforage.keys();
        const count = keys.length;
        
        const badge = document.getElementById('offline-sync-badge');
        if (badge) {
            if (count > 0) {
                badge.style.display = 'inline-block';
                badge.querySelector('.count').textContent = count;
            } else {
                badge.style.display = 'none';
            }
        }
    } catch (err) {
        console.error(err);
    }
}

// Mettre à jour le badge au chargement de la page
document.addEventListener('DOMContentLoaded', updateSyncBadge);
