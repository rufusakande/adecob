<?php

use App\Models\InfrastructureAssignment;
use App\Models\User;
use App\Models\MfaCode;

InfrastructureAssignment::where('assigned_to', 27)->delete();

foreach (['test_admin_multi@test.bj', 'test_agent_multi@test.bj'] as $email) {
    $u = User::where('email', $email)->first();
    if ($u) { MfaCode::where('user_id', $u->id)->delete(); $u->delete(); }
}

echo 'Users: ' . User::count() . ' | Affectations: ' . InfrastructureAssignment::count()
    . ' (dont Alban/9: ' . InfrastructureAssignment::where('assigned_to', 9)->count() . ')' . PHP_EOL;
