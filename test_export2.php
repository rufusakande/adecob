<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Infrastructure;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InfrastructuresExport;

$user = User::where('role', 'super_admin')->first();

// 1. PDF
$infrastructures = Infrastructure::query()->visibleTo($user)->get();
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

// 2. Excel
$query = Infrastructure::query()->visibleTo($user);
$start = microtime(true);
try {
    $excelOut = Excel::raw(new InfrastructuresExport($query, null, []), \Maatwebsite\Excel\Excel::XLSX);
    echo "Excel complet : " . round(strlen($excelOut) / 1024 / 1024, 2) . " Mo généré en " . round(microtime(true) - $start, 2) . "s\n";
} catch (\Exception $e) {
    echo "ERREUR Excel : " . $e->getMessage() . " en " . round(microtime(true) - $start, 2) . "s\n";
}
