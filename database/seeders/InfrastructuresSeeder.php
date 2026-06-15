<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InfrastructuresSeeder extends Seeder
{
    public function run()
    {
        $sql = file_get_contents(base_path('u591885416_roommmg.sql'));
        
        // Séparation des requêtes individuelles
        $queries = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($queries as $query) {
            if (
                stripos($query, 'INSERT INTO `infrastructures`') !== false ||
                stripos($query, 'INSERT INTO `infrastructure_works`') !== false ||
                stripos($query, 'INSERT INTO `mairie_agent_data`') !== false
            ) {
                DB::unprepared($query . ';');
            }
        }
    }
}