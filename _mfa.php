<?php

use App\Models\MfaCode;
use Illuminate\Support\Facades\Hash;

$u = App\Models\User::where('email', 'test_admin_multi@test.bj')->first();
if (!$u) { echo 'ADMIN INTROUVABLE' . PHP_EOL; return; }

MfaCode::where('user_id', $u->id)->whereNull('consumed_at')->update(['consumed_at' => now()]);
MfaCode::create([
    'user_id' => $u->id,
    'code_hash' => Hash::make('123456'),
    'expires_at' => now()->addMinutes(30),
    'ip' => '127.0.0.1',
]);
echo 'MFA régénéré pour admin ' . $u->id . ' = 123456' . PHP_EOL;
