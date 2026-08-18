<?php

use App\Models\User;
use App\Models\MfaCode;
use Illuminate\Support\Facades\Hash;

foreach (['test_admin_parakou@test.bj', 'test_agent_parakou@test.bj'] as $email) {
    $u = User::where('email', $email)->first();
    if ($u) {
        MfaCode::where('user_id', $u->id)->delete();
        $u->delete();
    }
}

$admin = new User();
$admin->forceFill([
    'name'     => 'Admin Test', 'prenom' => 'Test',
    'email'    => 'test_admin_parakou@test.bj',
    'telephone'=> '0102030406',
    'password' => Hash::make('Test@12345'),
    'role' => 'commune_admin', 'commune_id' => 1,
    'is_approved' => true, 'approved_at' => now(), 'email_verified_at' => now(),
]);
$admin->save();
MfaCode::create(['user_id' => $admin->id, 'code_hash' => Hash::make('123456'), 'expires_at' => now()->addMinutes(20), 'ip' => '127.0.0.1']);

$agent = new User();
$agent->forceFill([
    'name'     => 'Agent Test', 'prenom' => 'Test',
    'email'    => 'test_agent_parakou@test.bj',
    'telephone'=> '0102030407',
    'password' => Hash::make('Test@12345'),
    'role' => 'agent', 'commune_id' => 1,
    'is_approved' => true, 'approved_at' => now(), 'email_verified_at' => now(),
]);
$agent->save();

echo 'Admin id=' . $admin->id . ' | Agent id=' . $agent->id . PHP_EOL;
