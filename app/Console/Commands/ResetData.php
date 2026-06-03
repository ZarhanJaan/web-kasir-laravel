<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all business data and users in the database.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->confirm('WARNING: This will delete ALL stok data and users. Are you sure?')) {
            $this->info('Resetting database data...');

            // Disable foreign key checks to allow truncation if needed
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // List of tables to clear
            $tables = [
                't_riwayat_stok',
                't_stok_item',
                't_kategori',
                'users',
            ];

            foreach ($tables as $table) {
                if (\Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                    $this->line("Table cleared: <info>{$table}</info>");
                } else {
                    $this->line("Table not found, skipping: <comment>{$table}</comment>");
                }
            }

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->info('------------------------------------------');
            $this->info('Database reset completed successfully!');
            $this->info('All stok data and users have been cleared.');
            $this->info('------------------------------------------');

            return 0;
        }

        $this->info('Operation cancelled.');
        return 0;
    }
}
