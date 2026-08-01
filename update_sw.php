<?php
$file = __DIR__.'/public/service-worker.js';
$content = file_get_contents($file);

// Replace no-cors fetch with standard fetch
$content = str_replace(
    "const response = await fetch(new Request(url, { mode: 'no-cors' }));",
    "const response = await fetch(url);",
    $content
);

// Update cache version to v11
$content = str_replace(
    "const CACHE_NAME = 'infrastructure-offline-v10';",
    "const CACHE_NAME = 'infrastructure-offline-v11';",
    $content
);

file_put_contents($file, $content);
echo "Updated service-worker.js to v11 and removed no-cors\n";
