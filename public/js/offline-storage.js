// Configuration de localForage
localforage.config({
    name: 'ADECOB',
    storeName: 'infrastructures_offline',
    description: 'Stockage des infrastructures cr��es hors-ligne'
});

document.addEventListener('DOMContentLoaded', async function() {
    const form = document.getElementById('infraForm');
    
    // Create UI for showing pending items and success messages
    const uiContainer = document.createElement('div');
    uiContainer.id = 'offline-ui-container';
    uiContainer.className = 'mb-4';
    if(form) {
        form.parentNode.insertBefore(uiContainer, form);
    }
    
    await updatePendingCount();
    
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Check HTML5 validity
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                // Scroll to the first invalid element
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) firstInvalid.scrollIntoView({behavior: 'smooth', block: 'center'});
                return;
            }
            
            try {
                // Show saving state
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enregistrement...';
                submitBtn.disabled = true;

                // Generate a unique local ID
                const localId = 'offline_' + Date.now();
                const formData = new FormData(form);
                const data = { local_id: localId, timestamp: Date.now() };
                
                // Process all fields
                for (let [key, value] of formData.entries()) {
                    if (value instanceof File && value.size > 0) {
                        data[key] = await fileToBase64(value);
                    } else if (!(value instanceof File)) {
                        data[key] = value;
                    }
                }
                
                // Save to localForage
                let existingData = await localforage.getItem('pending_infrastructures') || [];
                existingData.push(data);
                await localforage.setItem('pending_infrastructures', existingData);
                
                // Reset UI
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
                form.reset();
                form.classList.remove('was-validated');
                window.scrollTo(0, 0);
                
                // Show success banner
                showSuccessBanner();
                await updatePendingCount();
                
            } catch (err) {
                console.error("Erreur de sauvegarde locale:", err);
                if (window.adecobUI) {
                    window.adecobUI.confirm({
                        title: 'Erreur de sauvegarde',
                        message: "Erreur lors de la sauvegarde sur l'appareil : " + (err && err.message ? err.message : 'inconnue'),
                        okText: 'OK',
                        icon: 'danger'
                    });
                } else {
                    alert("Erreur lors de la sauvegarde sur l'appareil: " + (err && err.message ? err.message : 'inconnue'));
                }
            }
        });
    }
});

async function updatePendingCount() {
    try {
        let existingData = await localforage.getItem('pending_infrastructures') || [];
        const container = document.getElementById('offline-ui-container');
        if (container) {
            const existingBadge = document.getElementById('pending-badge');
            if (existingData.length > 0) {
                const html = '<div id="pending-badge" class="alert alert-warning text-center fw-bold shadow-sm"><i class="bi bi-hdd-fill me-2"></i> Vous avez ' + existingData.length + ' infrastructure(s) sauvegard�e(s) sur cet appareil, en attente de synchronisation.</div>';
                if (existingBadge) {
                    existingBadge.outerHTML = html;
                } else {
                    container.insertAdjacentHTML('beforeend', html);
                }
            } else if (existingBadge) {
                existingBadge.remove();
            }
        }
    } catch(e) {}
}

function showSuccessBanner() {
    const container = document.getElementById('offline-ui-container');
    if (container) {
        const html = '<div class="alert alert-success alert-dismissible fade show text-center shadow-sm" role="alert"><h4 class="alert-heading fw-bold"><i class="bi bi-check-circle-fill me-2"></i> Sauvegarde r�ussie !</h4><p class="mb-0">L\'infrastructure a bien �t� enregistr�e sur votre t�l�phone.</p><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        container.insertAdjacentHTML('afterbegin', html);
    }
}

// Helper to convert File to Base64
function fileToBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = error => reject(error);
    });
}
