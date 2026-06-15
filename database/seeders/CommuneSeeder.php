<?php

namespace Database\Seeders;

use App\Models\Commune;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommuneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $communes = [
            ['name' => 'Parakou', 'logo' => null],
            ['name' => 'Cotonou', 'logo' => null],
            ['name' => 'Porto-Novo', 'logo' => null],
            ['name' => 'Abomey', 'logo' => null],
            ['name' => 'Djougou', 'logo' => null],
        ];

        foreach ($communes as $commune) {
            Commune::firstOrCreate(
                ['name' => $commune['name']],
                ['logo' => $commune['logo']]
            );
        }

        $this->command->info('Communes seeded successfully.');
    }
}
