<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
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
    protected $description = 'Create database, rebuild tables from SQL schema, and create initial owner user';

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
     * Create the database if it doesn't exist and rebuild tables based on web_kasir.sql.
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
            sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $host, $port),
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );

        $pdo->exec(sprintf(
            'CREATE DATABASE IF NOT EXISTS %s CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;',
            $this->quoteIdentifier($database)
        ));

        $this->info("Database '$database' is ready.");

        $pdo->exec('USE ' . $this->quoteIdentifier($database));

        $this->info('Rebuilding tables from SQL schema...');
        $this->rebuildSchema($pdo);
        $this->info('Database schema is ready.');
    }

    /**
     * Drop existing tables and create tables/columns according to the provided SQL dump.
     */
    protected function rebuildSchema(PDO $pdo)
    {
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($this->schemaTables() as $table) {
            $pdo->exec('DROP TABLE IF EXISTS ' . $this->quoteIdentifier($table) . ';');
        }

        $pdo->exec('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($this->schemaStatements() as $statement) {
            $pdo->exec($statement);
        }
    }

    protected function schemaTables()
    {
        return [
            'failed_jobs',
            'migrations',
            'password_resets',
            'personal_access_tokens',
            'qris',
            'roles',
            't_menu_resep',
            't_penjualan',
            't_produk',
            't_riwayat_stok',
            't_stok_item',
            'users',
        ];
    }

    protected function schemaStatements()
    {
        return [
            "CREATE TABLE `failed_jobs` (
                `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
                `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
                `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
                `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
                `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
                `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE `migrations` (
                `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `batch` int NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE `password_resets` (
                `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                KEY `password_resets_email_index` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE `personal_access_tokens` (
                `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
                `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `tokenable_id` bigint UNSIGNED NOT NULL,
                `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
                `abilities` text COLLATE utf8mb4_unicode_ci,
                `last_used_at` timestamp NULL DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
                KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE `qris` (
                `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
                `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE `roles` (
                `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
                `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "INSERT INTO `roles` (`id`, `role`, `created_at`, `updated_at`) VALUES
                (1, 'owner', NOW(), NOW()),
                (2, 'admin', NOW(), NOW()),
                (3, 'kasir', NOW(), NOW())",

            "CREATE TABLE `t_menu_resep` (
                `id_resep` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_menu` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `id_stok` bigint UNSIGNED NOT NULL,
                `jumlah` int NOT NULL DEFAULT '1',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id_resep`)
            ) ENGINE=InnoDB AUTO_INCREMENT=200302 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE `t_penjualan` (
                `id_penjualan` int UNSIGNED NOT NULL AUTO_INCREMENT,
                `tanggal` date NOT NULL,
                `nama_pelanggan` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
                `jumlah_barang` int NOT NULL,
                `id_produk` text COLLATE utf8mb4_general_ci,
                `total` decimal(60,0) NOT NULL,
                `metode_pembayaran` varchar(255) COLLATE utf8mb4_general_ci DEFAULT 'Cash',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id_penjualan`)
            ) ENGINE=InnoDB AUTO_INCREMENT=10004 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

            "CREATE TABLE `t_produk` (
                `id_produk` int NOT NULL,
                `nama_produk` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
                `stok` int DEFAULT NULL,
                `harga_beli` decimal(10,2) DEFAULT NULL,
                `harga_jual` decimal(10,2) DEFAULT NULL,
                `kategori` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
                PRIMARY KEY (`id_produk`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

            "CREATE TABLE `t_riwayat_stok` (
                `id_riwayat` int UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_produk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `id_stok` bigint UNSIGNED DEFAULT NULL,
                `jenis` enum('masuk','keluar') COLLATE utf8mb4_unicode_ci NOT NULL,
                `jumlah` int NOT NULL,
                `harga_beli` decimal(15,2) DEFAULT NULL,
                `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `tanggal` date NOT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                `nama_pelanggan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `total_harga` int DEFAULT NULL,
                PRIMARY KEY (`id_riwayat`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1000302 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE `t_stok_item` (
                `id_stok` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
                `nama_stok` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `stok` int NOT NULL DEFAULT '0',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id_stok`)
            ) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE `users` (
                `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
                `role_id` bigint UNSIGNED DEFAULT NULL,
                `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `email_verified_at` timestamp NULL DEFAULT NULL,
                `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `users_email_unique` (`email`),
                KEY `users_role_id_foreign` (`role_id`),
                CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        ];
    }

    protected function quoteIdentifier($identifier)
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
}
