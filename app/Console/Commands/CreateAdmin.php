<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    protected $signature = 'make:admin {email} {name}';
    protected $description = 'Créer un nouvel administrateur';

    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->argument('name');

        // Demander le mot de passe de manière sécurisée
        $password = $this->secret('Quel est le mot de passe pour le nouvel admin?');
        
        $validator = Validator::make([
            'email' => $email,
            'name' => $name,
            'password' => $password
        ], [
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|min:2',
            'password' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'super_admin',
            'is_approved' => true,
            'email_verified_at' => now(),
        ]);

        $this->info("Administrateur créé avec succès!");
        return 0;
    }
}