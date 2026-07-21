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

        // Schema dibuat langsung (bukan lewat artisan migrate), jadi migration
        // yang efeknya sudah tercakup di schema ini harus dicatat terlebih dahulu.
        $migrationNames = $this->schemaMigrationNames();
        sort($migrationNames);
        $insertMigration = $pdo->prepare('INSERT INTO migrations (migration, batch) VALUES (?, 1)');
        foreach ($migrationNames as $migrationName) {
            $insertMigration->execute([$migrationName]);
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
            't_kategori',
            't_menu_resep',
            't_penjualan',
            't_produk',
            't_riwayat_stok',
            't_stok_item',
            'users',
        ];
    }

    /**
     * Migrations whose effects are represented by this SQL schema.
     * This keeps a database created by app:setup compatible with artisan migrate.
     */
    protected function schemaMigrationNames()
    {
        return [
            '2014_10_12_000000_create_users_table',
            '2014_10_12_100000_create_password_resets_table',
            '2019_08_19_000000_create_failed_jobs_table',
            '2019_12_14_000001_create_personal_access_tokens_table',
            '2026_04_12_000000_create_core_tables',
            '2026_04_12_000001_add_timestamps_to_t_penjualan',
            '2026_04_12_000002_add_metode_pembayaran_to_t_penjualan',
            '2026_04_12_000003_create_roles_table',
            '2026_04_12_000004_add_role_id_to_users_table',
            '2026_04_12_000005_restore_auto_increment_to_users',
            '2026_04_18_000000_create_qris_table',
            '2026_05_01_000000_change_jumlah_barang_to_text_in_t_penjualan',
            '2026_05_13_000000_add_satuan_to_t_riwayat_stok',
            '2026_06_04_002950_create_t_kategori_table',
            '2026_07_21_000000_add_status_and_creator_audit_columns',
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

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
                `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
                PRIMARY KEY (`id`),
                UNIQUE KEY `roles_role_unique` (`role`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "INSERT INTO `roles` (`id`, `role`, `created_at`, `updated_at`) VALUES
                (1, 'owner', NOW(), NOW()),
                (2, 'admin', NOW(), NOW()),
                (3, 'kasir', NOW(), NOW())",

            "CREATE TABLE `t_kategori` (
                `id_kategori` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
                `nama_kategori` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id_kategori`),
                UNIQUE KEY `t_kategori_nama_kategori_unique` (`nama_kategori`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE `t_menu_resep` (
                `id_resep` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_menu` int UNSIGNED NOT NULL,
                `id_stok` bigint UNSIGNED NOT NULL,
                `jumlah` int UNSIGNED NOT NULL DEFAULT '1',
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT NULL,
                `created_by_id` bigint UNSIGNED DEFAULT NULL,
                `created_by_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                PRIMARY KEY (`id_resep`),
                KEY `t_menu_resep_id_menu_index` (`id_menu`),
                KEY `t_menu_resep_id_stok_index` (`id_stok`),
                KEY `t_menu_resep_created_by_id_index` (`created_by_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE `t_penjualan` (
                `id_penjualan` int UNSIGNED NOT NULL AUTO_INCREMENT,
                `tanggal` date NOT NULL,
                `nama_pelanggan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `jumlah_barang` text COLLATE utf8mb4_unicode_ci NOT NULL,
                `id_produk` text COLLATE utf8mb4_unicode_ci,
                `total` decimal(15,2) UNSIGNED NOT NULL,
                `metode_pembayaran` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Cash',
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT NULL,
                `created_by_id` bigint UNSIGNED DEFAULT NULL,
                `created_by_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                PRIMARY KEY (`id_penjualan`),
                KEY `t_penjualan_tanggal_index` (`tanggal`),
                KEY `t_penjualan_created_at_index` (`created_at`),
                KEY `t_penjualan_created_by_id_index` (`created_by_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE `t_produk` (
                `id_produk` int UNSIGNED NOT NULL,
                `nama_produk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `stok` int UNSIGNED NOT NULL DEFAULT '0',
                `harga_beli` decimal(15,2) UNSIGNED NOT NULL DEFAULT '0.00',
                `harga_jual` decimal(15,2) UNSIGNED NOT NULL DEFAULT '0.00',
                `kategori` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `created_by_id` bigint UNSIGNED DEFAULT NULL,
                `created_by_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                PRIMARY KEY (`id_produk`),
                KEY `t_produk_kategori_index` (`kategori`),
                KEY `t_produk_created_by_id_index` (`created_by_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE `t_riwayat_stok` (
                `id_riwayat` int UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_produk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `id_stok` bigint UNSIGNED DEFAULT NULL,
                `jenis` enum('masuk','keluar') COLLATE utf8mb4_unicode_ci NOT NULL,
                `jumlah` int UNSIGNED NOT NULL DEFAULT '1',
                `satuan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `harga_beli` decimal(15,2) UNSIGNED DEFAULT NULL,
                `keterangan` text COLLATE utf8mb4_unicode_ci,
                `tanggal` date NOT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                `nama_pelanggan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `total_harga` decimal(15,2) UNSIGNED NOT NULL DEFAULT '0.00',
                `created_by_id` bigint UNSIGNED DEFAULT NULL,
                `created_by_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                PRIMARY KEY (`id_riwayat`),
                KEY `t_riwayat_stok_tanggal_index` (`tanggal`),
                KEY `t_riwayat_stok_id_produk_index` (`id_produk`),
                KEY `t_riwayat_stok_id_stok_index` (`id_stok`),
                KEY `t_riwayat_stok_created_by_id_index` (`created_by_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE `t_stok_item` (
                `id_stok` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
                `nama_stok` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `stok` int UNSIGNED NOT NULL DEFAULT '0',
                `satuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pcs',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id_stok`),
                KEY `t_stok_item_nama_stok_index` (`nama_stok`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE `users` (
                `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
                `role_id` bigint UNSIGNED DEFAULT NULL,
                `status` tinyint(1) NOT NULL DEFAULT '1',
                `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `email_verified_at` timestamp NULL DEFAULT NULL,
                `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `remember_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
