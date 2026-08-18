<?php

use App\Models\Infrastructure;
use App\Models\InfrastructureAssignment;
use App\Models\User;
use App\Models\MfaCode;
use Illuminate\Support\Facades\Hash;

foreach (['test_admin_pk@test.bj', 'test_agent_pk@test.bj'] as $email) {
    $u = User::where('email', $email)->first();
    if ($u) { MfaCode::where('user_id', $u->id)->delete(); $u->delete(); }
}

$admin = new User();
$admin->forceFill([
    'name' => 'Admin PK', 'prenom' => 'Test', 'email' => 'test_admin_pk@test.bj',
    'telephone' => '0102030409', 'password' => Hash::make('Test@12345'),
    'role' => 'commune_admin', 'commune_id' => 1, 'is_approved' => true,
    'approved_at' => now(), 'email_verified_at' => now(),
]);
$admin->save();
MfaCode::create(['user_id' => $admin->id, 'code_hash' => Hash::make('123456'), 'expires_at' => now()->addMinutes(20), 'ip' => '127.0.0.1']);

$agent = new User();
$agent->forceFill([
    'name' => 'Agent PK', 'prenom' => 'Test', 'email' => 'test_agent_pk@test.bj',
    'telephone' => '0102030410', 'password' => Hash::make('Test@12345'),
    'role' => 'agent', 'commune_id' => 1, 'is_approved' => true,
    'approved_at' => now(), 'email_verified_at' => now(),
]);
$agent->save();

// Pré-affecter 2 infrastructures à l'agent (scénario « agent qui a déjà des affectations »)
$infraIds = Infrastructure::where('commune_id', 1)->orderBy('id', 'desc')->limit(2)->pluck('id');
foreach ($infraIds as $infraId) {
    InfrastructureAssignment::firstOrCreate(
        ['infrastructure_id' => $infraId, 'assigned_to' => $agent->id],
        ['assigned_by' => $admin->id, 'status' => InfrastructureAssignment::STATUS_ASSIGNED]
    );
}

echo 'Admin=' . $admin->id . ' | Agent=' . $agent->id . ' | Infras pré-affectées=' . $infraIds->implode(',') . PHP_EOL;
