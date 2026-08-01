<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$communes = ['Parakou', 'Tchaourou', "N'Dali", 'Nikki', 'Bembéréké', 'Kalalé', 'Sinendé', 'Péréré'];
try {
    $html = view('infrastructures.offline-create-template', [
        'communeNames' => $communes,
        'errors' => new \Illuminate\Support\MessageBag()
    ])->render();

    $html = preg_replace('/<input type="hidden" name="_token" value="[^"]+">/', '', $html);
    $html = preg_replace('/action="[^"]+"/', 'action="#"', $html);
    $scripts = '<script src="https://cdnjs.cloudflare.com/ajax/libs/localforage/1.10.0/localforage.min.js"></script><script src="/js/offline-storage.js"></script>';
    $html = str_replace('</body>', $scripts . '</body>', $html);
    file_put_contents(__DIR__.'/public/offline.html', $html);
    echo "offline.html généré avec succès!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
