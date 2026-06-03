<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

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
    protected $description = 'Reset all business data, users, and QRIS in the database, and clean QRIS image folder.';

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
        if ($this->confirm('WARNING: This will delete ALL transaction data, products, users, and QRIS. Are you sure?')) {
            $this->info('Resetting database data...');

            // Disable foreign key checks to allow truncation if needed
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // List of tables to clear
            $tables = [
                't_penjualan',
                't_produk',
                't_riwayat_stok',
                't_stok_item',
                't_menu_resep',
                'users',
                'qris',
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

            // Clean QRIS image folder
            $qrisPath = public_path('images/qris');
            if (File::isDirectory($qrisPath)) {
                File::cleanDirectory($qrisPath);
                $this->line("Folder cleaned: <info>public/images/qris</info>");
            } else {
                $this->line("Folder not found, skipping: <comment>public/images/qris</comment>");
            }

            $this->info('------------------------------------------');
            $this->info('Database reset completed successfully!');
            $this->info('All data, users, and QRIS have been cleared.');
            $this->info('------------------------------------------');

            return 0;
        }

        $this->info('Operation cancelled.');
        return 0;
    }
}
