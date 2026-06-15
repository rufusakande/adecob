<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MairieAgentData;

class MairieAgentDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MairieAgentData::create([
            'infrastructure_id' => 1,
            'nom_enqueteur' => 'Jean Dupont',
            'commune' => 'Parakou',
            'secteur' => 'Infrastructures',
            'designation' => 'Pont Principal',
            'localisation' => 'Centre-ville',
            'activites' => 'Maintenance',
            'responsables' => 'Jean Dupont',
            'personnes_associes' => 5,
            'source_financement' => 'Budget Municipal',
            'montant' => 150000.00,
            'periode_2023' => true,
            'periode_2024' => false,
            'periode_2025' => false,
        ]);
    }
}
