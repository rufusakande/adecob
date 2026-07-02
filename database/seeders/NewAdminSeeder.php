<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class NewAdminSeeder extends Seeder
{
    public function run()
    {
        // Affectation directe pour les champs privilégiés (hors $fillable).
        $user = new User([
            'name'     => 'Adecob Admin',
            'email'    => 'akanderufus51@gmail.com',
            'password' => Hash::make('admin123'),
        ]);
        $user->role               = 'super_admin';
        $user->is_approved        = true;
        $user->email_verified_at  = now();
        $user->save();
    }
}
