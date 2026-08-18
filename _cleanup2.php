<?php

use App\Models\InfrastructureAssignment;
use App\Models\User;
use App\Models\MfaCode;

// Supprimer les affectations de test (infra 9497 → agent 20)
InfrastructureAssignment::where('assigned_by', 19)->delete();

// Supprimer les utilisateurs de test
foreach (['test_admin_parakou@test.bj', 'test_agent_parakou@test.bj'] as $email) {
    $u = User::where('email', $email)->first();
    if ($u) {
        MfaCode::where('user_id', $u->id)->delete();
        $u->delete();
    }
}

echo 'Users: ' . User::count() . ' | Affectations: ' . InfrastructureAssignment::count() . PHP_EOL;
