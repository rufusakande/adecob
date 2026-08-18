<?php

use App\Models\InfrastructureAssignment;
use App\Models\User;
use App\Models\MfaCode;

// Supprimer UNIQUEMENT les affectations créées par l'admin de test (21)
InfrastructureAssignment::where('assigned_by', 21)->delete();

// Supprimer les utilisateurs de test (sauf Alban id=9 et Akande id=1)
foreach (['test_admin_parakou@test.bj', 'test_agent_parakou@test.bj', 'test_agent2_parakou@test.bj'] as $email) {
    $u = User::where('email', $email)->first();
    if ($u) { MfaCode::where('user_id', $u->id)->delete(); $u->delete(); }
}

echo 'Users: ' . User::count() . ' | Affectations: ' . InfrastructureAssignment::count() . PHP_EOL;
echo 'Affectations Alban (9): ' . InfrastructureAssignment::where('assigned_to', 9)->count() . PHP_EOL;
