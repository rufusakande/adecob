<?php

use App\Models\InfrastructureAssignment;

$all = InfrastructureAssignment::orderByDesc('id')->get(['id', 'infrastructure_id', 'assigned_to', 'assigned_by', 'status', 'created_at']);
echo 'Total: ' . $all->count() . PHP_EOL;
foreach ($all->take(15) as $a) {
    echo $a->id . ' | infra=' . $a->infrastructure_id . ' | agent=' . $a->assigned_to . ' | by=' . $a->assigned_by . ' | ' . $a->status . ' | ' . $a->created_at . PHP_EOL;
}
