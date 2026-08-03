localforage.config({
    name: 'ADECOB',
    storeName: 'infrastructures_offline',
    description: 'Stockage des infrastructures créées hors-ligne',
});

function offlineFileToBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result);
        reader.onerror = () => reject(reader.error || new Error('Lecture du fichier impossible'));
        reader.readAsDataURL(file);
    });
}

async function collectOfflineForm(form) {
    const data = { local_id: 'offline_' + Date.now(), timestamp: Date.now() };
    for (const [key, value] of new FormData(form).entries()) {
        if (key === '_token' || key === '_method') continue;
        if (value instanceof File && value.size === 0) continue;
        const storedValue = value instanceof File ? await offlineFileToBase64(value) : value;
        if (Object.prototype.hasOwnProperty.call(data, key)) {
            data[key] = Array.isArray(data[key]) ? data[key].concat(storedValue) : [data[key], storedValue];
        } else {
            data[key] = storedValue;
        }
    }
    return data;
}

async function renderOfflineFormStatus(container) {
    if (!container) return;
    const items = await getOfflineQueue();
    container.innerHTML = items.length
        ? '<div class="alert alert-warning text-center fw-bold shadow-sm">Vous avez ' + items.length + ' infrastructure(s) sauvegardée(s) sur cet appareil, en attente de synchronisation.</div>'
        : '';
}

document.addEventListener('DOMContentLoaded', async function () {
    const form = document.getElementById('infraForm');
    if (!form || form.dataset.offlineCaptureReady === 'true') return;
    form.dataset.offlineCaptureReady = 'true';

    const container = document.createElement('div');
    container.id = 'offline-ui-container';
    container.className = 'mb-4';
    form.parentNode.insertBefore(container, form);
    await renderOfflineFormStatus(container);

    form.addEventListener('submit', async function (event) {
        if (navigator.onLine) return;
        event.preventDefault();

        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            const firstInvalid = form.querySelector(':invalid');
            if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        const submitButton = form.querySelector('button[type="submit"]');
        const originalLabel = submitButton ? submitButton.innerHTML : '';
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Enregistrement local…';
        }

        try {
            const items = await getOfflineQueue();
            items.push(await collectOfflineForm(form));
            await setOfflineQueue(items);
            form.reset();
            form.classList.remove('was-validated');
            await renderOfflineFormStatus(container);
            container.insertAdjacentHTML('afterbegin', '<div class="alert alert-success text-center shadow-sm"><strong>Sauvegarde réussie.</strong> La fiche reste sur cet appareil jusqu’à sa synchronisation.</div>');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } catch (error) {
            container.insertAdjacentHTML('afterbegin', '<div class="alert alert-danger">Impossible de sauvegarder la fiche sur cet appareil. Vérifiez l’espace de stockage disponible.</div>');
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = originalLabel;
            }
        }
    });
});