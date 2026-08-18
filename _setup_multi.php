<?php

use App\Models\InfrastructureAssignment;
use App\Models\User;
use App\Models\MfaCode;
use Illuminate\Support\Facades\Hash;

foreach (['test_admin_multi@test.bj', 'test_agent_multi@test.bj'] as $email) {
    $u = User::where('email', $email)->first();
    if ($u) { MfaCode::where('user_id', $u->id)->delete(); $u->delete(); }
}

$admin = new User();
$admin->forceFill([
    'name' => 'Admin Multi', 'prenom' => 'Test', 'email' => 'test_admin_multi@test.bj',
    'telephone' => '0102030411', 'password' => Hash::make('Test@12345'),
    'role' => 'commune_admin', 'commune_id' => 1, 'is_approved' => true,
    'approved_at' => now(), 'email_verified_at' => now(),
]);
$admin->save();
MfaCode::create(['user_id' => $admin->id, 'code_hash' => Hash::make('123456'), 'expires_at' => now()->addMinutes(20), 'ip' => '127.0.0.1']);

$agent = new User();
$agent->forceFill([
    'name' => 'Agent Multi', 'prenom' => 'Test', 'email' => 'test_agent_multi@test.bj',
    'telephone' => '0102030412', 'password' => Hash::make('Test@12345'),
    'role' => 'agent', 'commune_id' => 1, 'is_approved' => true,
    'approved_at' => now(), 'email_verified_at' => now(),
]);
$agent->save();

// Supprimer toute affectation existante de cet agent de test
InfrastructureAssignment::where('assigned_to', $agent->id)->delete();

echo 'Admin=' . $admin->id . ' | Agent=' . $agent->id . PHP_EOL;
