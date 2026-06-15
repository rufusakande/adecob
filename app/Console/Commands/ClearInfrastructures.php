<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearInfrastructures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'infrastructures:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all records from the infrastructures table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        set_time_limit(0); // Remove time limit for long operations

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('infrastructures')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('All records deleted from infrastructures table.');

        return 0;
    }
}
