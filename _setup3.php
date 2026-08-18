<?php

use App\Models\User;
use App\Models\MfaCode;
use Illuminate\Support\Facades\Hash;

foreach (['test_admin_parakou@test.bj', 'test_agent_parakou@test.bj', 'test_agent2_parakou@test.bj'] as $email) {
    $u = User::where('email', $email)->first();
    if ($u) { MfaCode::where('user_id', $u->id)->delete(); $u->delete(); }
}

$admin = new User();
$admin->forceFill([
    'name' => 'Admin Test', 'prenom' => 'Test', 'email' => 'test_admin_parakou@test.bj',
    'telephone' => '0102030406', 'password' => Hash::make('Test@12345'),
    'role' => 'commune_admin', 'commune_id' => 1, 'is_approved' => true,
    'approved_at' => now(), 'email_verified_at' => now(),
]);
$admin->save();
MfaCode::create(['user_id' => $admin->id, 'code_hash' => Hash::make('123456'), 'expires_at' => now()->addMinutes(20), 'ip' => '127.0.0.1']);

$agent1 = new User();
$agent1->forceFill([
    'name' => 'Agent Un', 'prenom' => 'Test', 'email' => 'test_agent_parakou@test.bj',
    'telephone' => '0102030407', 'password' => Hash::make('Test@12345'),
    'role' => 'agent', 'commune_id' => 1, 'is_approved' => true,
    'approved_at' => now(), 'email_verified_at' => now(),
]);
$agent1->save();

$agent2 = new User();
$agent2->forceFill([
    'name' => 'Agent Deux', 'prenom' => 'Test', 'email' => 'test_agent2_parakou@test.bj',
    'telephone' => '0102030408', 'password' => Hash::make('Test@12345'),
    'role' => 'agent', 'commune_id' => 1, 'is_approved' => true,
    'approved_at' => now(), 'email_verified_at' => now(),
]);
$agent2->save();

echo 'Admin=' . $admin->id . ' | Agent1=' . $agent1->id . ' | Agent2=' . $agent2->id . PHP_EOL;
