<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if super admin already exists
        if (User::where('email', 'admin@adecob.com')->exists()) {
            $this->command->info('Super Admin already exists.');
            return;
        }

        User::create([
            'name' => 'Admin ADECOB',
            'email' => 'admin@adecob.com',
            'password' => Hash::make('password'), // Change this in production
            'role' => 'super_admin',
            'is_approved' => true,
        ]);

        $this->command->info('Super Admin created successfully.');
    }
}
