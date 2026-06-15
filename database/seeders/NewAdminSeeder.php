<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class NewAdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Adecob Admin',
            'email' => 'akanderufus51@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'super_admin',
            'is_approved' => true,
            'email_verified_at' => now(),
        ]);
    }
}