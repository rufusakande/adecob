<?php

use App\Models\InfrastructureAssignment;

$assigns = InfrastructureAssignment::where('assigned_to', 25)->get(['id', 'infrastructure_id', 'status']);
echo 'Affectations agent 25 (' . $assigns->count() . '): ' . $assigns->map(fn ($a) => $a->infrastructure_id . '/' . $a->status)->implode(', ') . PHP_EOL;
echo '9496 pour agent 25 ? ' . (InfrastructureAssignment::where('infrastructure_id', 9496)->where('assigned_to', 25)->exists() ? 'OUI' : 'NON') . PHP_EOL;
