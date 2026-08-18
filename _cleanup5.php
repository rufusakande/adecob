<?php

use App\Models\InfrastructureAssignment;
use App\Models\User;
use App\Models\MfaCode;

// Supprimer les affectations créées par l'admin de test (24)
InfrastructureAssignment::where('assigned_by', 24)->delete();

// Supprimer les utilisateurs de test
foreach (['test_admin_pk@test.bj', 'test_agent_pk@test.bj'] as $email) {
    $u = User::where('email', $email)->first();
    if ($u) { MfaCode::where('user_id', $u->id)->delete(); $u->delete(); }
}

echo 'Users: ' . User::count() . ' | Affectations: ' . InfrastructureAssignment::count()
    . ' (dont Alban/9: ' . InfrastructureAssignment::where('assigned_to', 9)->count() . ')' . PHP_EOL;
