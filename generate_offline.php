<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/offline-generator', 'GET');
$app['router']->get('/offline-generator', function() {
    $communes = ['Parakou', 'Tchaourou', 'N\'Dali', 'Nikki', 'Bembèrèkè', 'Kalalé', 'Sinendé', 'Pèrèrè'];
    return view('infrastructures.offline-create-template', [
        'communeNames' => $communes,
        'errors' => new \Illuminate\Support\MessageBag()
    ])->render();
});
$response = $kernel->handle($request);
$html = $response->getContent();
if (strpos($html, '<form') === false) { echo "Erreur: Formulaire non trouvé dans le HTML\n"; exit(1); }
$html = preg_replace('/<input type="hidden" name="_token" value="[^"]+">/', '', $html);
$html = preg_replace('/action="[^"]+"/', 'action="#"', $html);
$scripts = '<script src="https://cdnjs.cloudflare.com/ajax/libs/localforage/1.10.0/localforage.min.js"></script><script src="/js/offline-storage.js"></script>';
$html = str_replace('</body>', $scripts . '</body>', $html);
file_put_contents(__DIR__.'/public/offline.html', $html);
echo "offline.html généré avec succès!\n";


