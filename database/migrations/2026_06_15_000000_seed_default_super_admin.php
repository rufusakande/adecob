<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Seed the default Super Admin (Akande Rufus) on migrate.
     * Idempotent: replaces any existing user with the same email.
     */
    public function up(): void
    {
        $email = 'akanderufus51@gmail.com';

        $existing = DB::table('users')->where('email', $email)->first();

        $payload = [
            'name'        => 'Akande Rufus',
            'email'       => $email,
            'password'    => Hash::make('@Wxcvbn741@'),
            'role'        => 'super_admin',
            'is_approved' => true,
            'commune_id'  => null,
            'updated_at'  => now(),
        ];

        if ($existing) {
            DB::table('users')->where('email', $email)->update($payload);
        } else {
            $payload['created_at'] = now();
            DB::table('users')->insert($payload);
        }
    }

    public function down(): void
    {
        DB::table('users')->where('email', 'akanderufus51@gmail.com')->delete();
    }
};
