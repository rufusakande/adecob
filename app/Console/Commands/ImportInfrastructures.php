<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\InfrastructuresImport;

class ImportInfrastructures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'infrastructures:import {file : The path to the Excel file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import infrastructures data from an Excel file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        set_time_limit(0); // Remove time limit for long imports

        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File not found: $file");
            return 1;
        }

        try {
            Excel::import(new InfrastructuresImport, $file);
            $this->info('Import completed successfully.');
            return 0;
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            foreach ($failures as $failure) {
                $this->error('Row ' . $failure->row() . ': ' . implode(', ', $failure->errors()));
            }
            return 1;
        } catch (\Exception $e) {
            $this->error('Error during import: ' . $e->getMessage());
            return 1;
        }
    }
}
