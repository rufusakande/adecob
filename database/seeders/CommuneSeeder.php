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
            ['name' => 'N\'Dali', 'logo' => null],
            ['name' => 'Tchaourou', 'logo' => null],
            ['name' => 'Bembereke', 'logo' => null],
            ['name' => 'Nikki', 'logo' => null],
            ['name' => 'Kalale', 'logo' => null],
            ['name' => 'Perere', 'logo' => null],
            ['name' => 'Sinende', 'logo' => null],
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
