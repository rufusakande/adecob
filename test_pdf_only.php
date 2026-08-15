<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', '-1'); // mémoire illimitée pour le diagnostic
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Infrastructure;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

$user = User::where('role', 'super_admin')->first();
$infrastructures = Infrastructure::query()->visibleTo($user)->get();
echo "Chargement : " . $infrastructures->count() . " infra\n";

$start = microtime(true);
try {
    $pdf = Pdf::loadView('infrastructures.export_pdf', [
        'infrastructures' => $infrastructures,
        'filters'         => [],
        'year'            => null,
    ]);
    $out = $pdf->output();
    echo "PDF OK : " . round(strlen($out) / 1024 / 1024, 2) . " Mo en " . round(microtime(true) - $start, 2) . "s\n";
    echo "Pic mémoire : " . round(memory_get_peak_usage(true) / 1024 / 1024, 0) . " Mo\n";
} catch (\Exception $e) {
    echo "ERREUR PDF : " . $e->getMessage() . "\n";
    echo "Pic mémoire : " . round(memory_get_peak_usage(true) / 1024 / 1024, 0) . " Mo\n";
}
