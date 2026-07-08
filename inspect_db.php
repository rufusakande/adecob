<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$db = app('db');
$infra = $db->select('SHOW COLUMNS FROM infrastructures');
$comm = $db->select('SHOW COLUMNS FROM communes');
echo "INFRA COLUMNS:\n";
foreach ($infra as $r) {
    echo $r->Field.' '.$r->Type.' '.($r->Null === 'NO' ? 'NOT NULL' : 'NULL')."\n";
}
echo "COMMUNE COLUMNS:\n";
foreach ($comm as $r) {
    echo $r->Field.' '.$r->Type.' '.($r->Null === 'NO' ? 'NOT NULL' : 'NULL')."\n";
}
echo 'COUNTS: infra=' . $db->table('infrastructures')->count() . ' communes=' . $db->table('communes')->count() . "\n";
