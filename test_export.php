<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Infrastructure;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

$user = User::where('role', 'super_admin')->first();
echo "Super admin : {$user->name}\n\n";

// 1. Chargement
$start = microtime(true);
$infrastructures = Infrastructure::query()->visibleTo($user)->get();
echo "Chargement : {$infrastructures->count()} infra en " . round(microtime(true) - $start, 2) . "s\n";

// 2. Coût d'UNE sauvegarde (simulé en transaction annulée pour ne pas polluer)
$start = microtime(true);
DB::beginTransaction();
$infrastructures->first()->incrementExportCount();
DB::rollBack();
$oneSave = microtime(true) - $start;
echo "Coût 1 save() : " . round($oneSave * 1000, 2) . " ms  => estimé pour {$infrastructures->count()} lignes : " . round($oneSave * $infrastructures->count() / 60, 1) . " min\n";

// 3. Bulk update
$start = microtime(true);
$ids = $infrastructures->pluck('id')->filter();
DB::table('infrastructures')->whereIn('id', $ids)->update([
    'exported_at' => now(),
    'export_count' => DB::raw('COALESCE(export_count, 0) + 1'),
]);
echo "Bulk update ({$ids->count()} lignes) : " . round(microtime(true) - $start, 2) . "s\n";

// 4. Génération PDF complète
$start = microtime(true);
try {
    $pdf = Pdf::loadView('infrastructures.export_pdf', [
        'infrastructures' => $infrastructures,
        'filters'         => [],
        'year'            => null,
    ]);
    $out = $pdf->output();
    echo "PDF complet : " . round(strlen($out) / 1024 / 1024, 2) . " Mo généré en " . round(microtime(true) - $start, 2) . "s\n";
} catch (\Exception $e) {
    echo "ERREUR PDF : " . $e->getMessage() . " en " . round(microtime(true) - $start, 2) . "s\n";
}
