document.addEventListener('DOMContentLoaded', async function() {
    // Check if there are pending items
    const pendingData = await localforage.getItem('pending_infrastructures') || [];
    
    if (pendingData.length > 0) {
        // Add a sync button to the navbar
        const navbar = document.querySelector('.navbar-nav');
        if (navbar) {
            const syncLi = document.createElement('li');
            syncLi.className = 'nav-item ms-2';
            syncLi.innerHTML = `<a href="#" class="nav-link btn btn-warning text-dark px-3 py-1 fw-bold" id="offline-sync-badge" title="Cliquez pour synchroniser maintenant">
                <i class="bi bi-cloud-arrow-up-fill me-1"></i> <span class="count">` + pendingData.length + `</span> hors-ligne
            </a>`;
            navbar.appendChild(syncLi);
            
            document.getElementById('offline-sync-badge').addEventListener('click', async function(e) {
                e.preventDefault();
                if (!navigator.onLine) {
                    alert("Vous devez être connecté à internet pour synchroniser.");
                    return;
                }
                
                if (confirm("Voulez-vous synchroniser " + pendingData.length + " infrastructure(s) avec le serveur ?")) {
                    this.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Sync...';
                    this.style.pointerEvents = 'none';
                    await syncOfflineData();
                }
            });
        }
    }
});

async function syncOfflineData() {
    let pendingData = await localforage.getItem('pending_infrastructures') || [];
    if (pendingData.length === 0) return;
    
    let successCount = 0;
    let csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        // If not in meta, try to get it from an existing form
        let inputToken = document.querySelector('input[name="_token"]');
        csrfToken = inputToken ? inputToken.value : '';
    } else {
        csrfToken = csrfToken.getAttribute('content');
    }
    
    // We need an array to keep items that failed
    let remainingData = [];
    
    for (let data of pendingData) {
        try {
            // Convert JS object to FormData for normal Laravel POST
            const formData = new FormData();
            formData.append('_token', csrfToken);
            
            let photosBase64 = [];
            
            for (const [key, value] of Object.entries(data)) {
                if (key === 'local_id' || key === 'timestamp') continue;
                
                // If it's a photo that was converted to base64
                if (key.startsWith('photo') && typeof value === 'string' && value.startsWith('data:image')) {
                    photosBase64.push(value);
                } else if (Array.isArray(value)) {
                    value.forEach(v => formData.append(key + '[]', v));
                } else {
                    formData.append(key, value);
                }
            }
            
            // Add base64 photos to photos_data for the controller to handle
            if (photosBase64.length > 0) {
                formData.append('photos_data', JSON.stringify(photosBase64));
            }
            
            const response = await fetch('/infrastructures', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok || response.status === 302) {
                successCount++;
            } else {
                console.error("Erreur serveur pour", data.local_id, response.status);
                remainingData.push(data); // Keep it to retry later
            }
        } catch (e) {
            console.error("Erreur réseau pour", data.local_id, e);
            remainingData.push(data); // Keep it to retry later
        }
    }
    
    // Update local storage
    await localforage.setItem('pending_infrastructures', remainingData);
    
    if (successCount > 0) {
        alert(successCount + " infrastructure(s) synchronisée(s) avec succès !");
        location.reload();
    } else {
        alert("La synchronisation a échoué. Veuillez réessayer plus tard.");
        location.reload();
    }
}
