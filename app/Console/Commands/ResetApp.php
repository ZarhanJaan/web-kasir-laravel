<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use PDO;
use Exception;

class ResetApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create database if not exists, reset tables and seed default roles';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will WIPE all data in the database. Do you want to proceed?', false)) {
                $this->info('Reset cancelled.');
                return 0;
            }
        }

        try {
            $this->createDatabase();
        } catch (Exception $e) {
            $this->error('Could not create database: ' . $e->getMessage());
            return 1;
        }

        $this->info('Resetting database tables...');
        Artisan::call('migrate:fresh', ['--force' => true]);
        $this->info(Artisan::output());

        $this->info('Seeding roles...');
        Artisan::call('db:seed', ['--class' => 'RoleSeeder', '--force' => true]);
        $this->info(Artisan::output());

        $this->info('Database has been reset and roles have been seeded.');
        return 0;
    }

    /**
     * Create the database if it doesn't exist.
     */
    protected function createDatabase()
    {
        $connection = config('database.default');
        $config = config("database.connections.{$connection}");

        $host = $config['host'];
        $port = $config['port'];
        $database = $config['database'];
        $username = $config['username'];
        $password = $config['password'];

        $this->info("Ensuring database '$database' exists...");

        // Connect to MySQL without selecting a database
        $pdo = new PDO(
            sprintf('mysql:host=%s;port=%d', $host, $port),
            $username,
            $password
        );

        $pdo->exec(sprintf(
            'CREATE DATABASE IF NOT EXISTS %s CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;',
            $database
        ));

        $this->info("Database '$database' is ready.");
    }
}
