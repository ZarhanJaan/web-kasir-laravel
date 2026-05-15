<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Artisan;
use PDO;
use Exception;

class SetupApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create database if not exists, reset and create initial owner user';

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
        if (!$this->confirm('This will ensure the database exists, WIPE all existing data, and setup a new Owner account. Proceed?', false)) {
            $this->info('Setup cancelled.');
            return 0;
        }

        try {
            $this->createDatabase();
        } catch (Exception $e) {
            $this->error('Could not create database: ' . $e->getMessage());
            return 1;
        }

        $this->info('Resetting database tables and seeding roles...');
        // Calls the reset command which handles migration and role seeding
        Artisan::call('app:reset', ['--force' => true]);
        $this->info(Artisan::output());

        $this->info('--- Setup Initial Owner User ---');

        $username = $this->ask('Enter Gmail username (e.g. admin)');

        if (substr($username, -10) !== '@gmail.com') {
            $email = $username . '@gmail.com';
        } else {
            $email = $username;
        }

        $password = $this->secret('Enter Password');
        if (empty($password)) {
            $this->error('Password cannot be empty.');
            return 1;
        }

        $roleOwner = Role::where('role', 'owner')->first();
        if (!$roleOwner) {
            $this->error('Role "owner" not found.');
            return 1;
        }

        try {
            User::create([
                'name' => 'Owner',
                'email' => $email,
                'password' => Hash::make($password),
                'role_id' => $roleOwner->id,
            ]);
            $this->info("User created successfully: $email");
            $this->info('Application setup complete!');
        } catch (\Exception $e) {
            $this->error('Failed to create user: ' . $e->getMessage());
            return 1;
        }

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

        $this->info("Checking database '$database'...");

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
