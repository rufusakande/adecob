<?php

use App\Models\InfrastructureAssignment;

// Réinitialiser : garder seulement 9497 et 6295 pour l'agent 25
InfrastructureAssignment::where('assigned_to', 25)
    ->whereNotIn('infrastructure_id', [9497, 6295])
    ->delete();

$assigns = InfrastructureAssignment::where('assigned_to', 25)->get(['infrastructure_id', 'status']);
echo 'Agent 25 (' . $assigns->count() . '): ' . $assigns->map(fn ($a) => $a->infrastructure_id)->implode(',') . PHP_EOL;
