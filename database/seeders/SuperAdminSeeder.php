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
        // Les champs privilégiés (role, is_approved, commune_id) sont hors $fillable.
        // On passe par forceFill() pour les seeders CLI (contexte de confiance).
        $user = User::firstOrNew(['email' => 'akanderufus51@gmail.com']);
        $user->fill([
            'name'     => 'Akande Rufus',
            'password' => Hash::make('@Wxcvbn741@'),
        ]);
        $user->role        = 'super_admin';
        $user->is_approved = true;
        $user->commune_id  = null;
        $user->save();

        $this->command->info('Super Admin (Akande Rufus) seeded / updated successfully.');
    }
}
