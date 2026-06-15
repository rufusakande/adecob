<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Seed the default Super Admin (Akande Rufus).
     * Idempotent: updates existing record if present.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'akanderufus51@gmail.com'],
            [
                'name'        => 'Akande Rufus',
                'password'    => Hash::make('@Wxcvbn741@'),
                'role'        => 'super_admin',
                'is_approved' => true,
                'commune_id'  => null,
            ]
        );

        $this->command->info('Super Admin (Akande Rufus) seeded / updated successfully.');
    }
}
