<?php
$file = __DIR__.'/public/js/offline-sync.js';
$content = file_get_contents($file);
$bad = 'syncLi.innerHTML = <a href="#" class="nav-link btn btn-warning text-dark px-3 py-1 fw-bold" id="offline-sync-badge" title="Cliquez pour synchroniser maintenant">
                <i class="bi bi-cloud-arrow-up-fill me-1"></i> <span class="count"> + pendingData.length + </span> hors-ligne
            </a>;';
$good = 'syncLi.innerHTML = `<a href="#" class="nav-link btn btn-warning text-dark px-3 py-1 fw-bold" id="offline-sync-badge" title="Cliquez pour synchroniser maintenant">
                <i class="bi bi-cloud-arrow-up-fill me-1"></i> <span class="count">` + pendingData.length + `</span> hors-ligne
            </a>`;';
if (strpos($content, $bad) !== false) {
    file_put_contents($file, str_replace($bad, $good, $content));
    echo "Fixed offline-sync.js\n";
} else {
    echo "Bad string not found.\n";
}
