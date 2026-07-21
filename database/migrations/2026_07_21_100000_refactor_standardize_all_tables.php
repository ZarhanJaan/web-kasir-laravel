<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RefactorStandardizeAllTables extends Migration
{
    /**
     * Refactoring & Standarisasi struktur tabel:
     *
     * PERUBAHAN:
     * - t_produk.nama_produk      : varchar(100) → varchar(255)
     * - t_penjualan.nama_pelanggan: varchar(30)  → varchar(255)
     * - t_penjualan.total         : decimal(60,0)→ decimal(15,2)
     * - t_riwayat_stok.total_harga: int(11)      → decimal(15,2)
     * - t_riwayat_stok.keterangan : varchar(255) → text
     * - t_menu_resep.jumlah       : int(11)      → decimal(10,2)  [bahan bisa pecahan]
     * - t_stok_item.satuan        : tambahkan jika belum ada (varchar(255))
     *
     * TIDAK DIUBAH:
     * - Semua PRIMARY KEY & AUTO_INCREMENT
     * - Tipe ENUM pada t_riwayat_stok.jenis ('masuk','keluar')
     * - Kolom relasi (id_stok, id_menu, role_id, dll)
     * - Tabel sistem Laravel (users, migrations, failed_jobs, dll)
     * - t_penjualan.id_produk & jumlah_barang (TEXT — sengaja untuk comma-separated)
     */
    public function up()
    {
        // ── 1. t_produk ──────────────────────────────────────────────────────
        Schema::table('t_produk', function (Blueprint $table) {
            // nama_produk: varchar(100) → varchar(255)
            $table->string('nama_produk', 255)->nullable()->change();

            // harga_beli & harga_jual sudah decimal(10,2) → konsisten, tidak diubah
            // stok sudah int(11) → wajar untuk stok, tidak diubah
        });

        // ── 2. t_penjualan ───────────────────────────────────────────────────
        Schema::table('t_penjualan', function (Blueprint $table) {
            // nama_pelanggan: varchar(30) → varchar(255)
            $table->string('nama_pelanggan', 255)->change();

            // total: decimal(60,0) → decimal(15,2) [presisi keuangan yang benar]
            $table->decimal('total', 15, 2)->change();

            // metode_pembayaran: sudah varchar(255) → tidak diubah
        });

        // ── 3. t_riwayat_stok ───────────────────────────────────────────────
        Schema::table('t_riwayat_stok', function (Blueprint $table) {
            // total_harga: int(11) → decimal(15,2)  [harga adalah uang, bukan integer]
            $table->decimal('total_harga', 15, 2)->nullable()->change();

            // keterangan: varchar(255) → text  [keterangan bisa panjang]
            $table->text('keterangan')->nullable()->change();

            // satuan: sudah varchar(255) → tidak diubah
            // jenis: enum('masuk','keluar') → TIDAK DIUBAH (relasi penting)
            // nama_pelanggan: sudah varchar(255) → tidak diubah
        });

        // ── 4. t_menu_resep ─────────────────────────────────────────────────
        Schema::table('t_menu_resep', function (Blueprint $table) {
            // jumlah: int(11) → decimal(10,2)  [bahan baku bisa berupa pecahan, mis. 0.5 liter]
            $table->decimal('jumlah', 10, 2)->default(1)->change();

            // id_menu: varchar(255) → sudah varchar(255), tidak diubah
        });

        // ── 5. t_stok_item ──────────────────────────────────────────────────
        // Tambahkan kolom satuan jika belum ada (kadang tidak ada di DB lama)
        if (!Schema::hasColumn('t_stok_item', 'satuan')) {
            Schema::table('t_stok_item', function (Blueprint $table) {
                $table->string('satuan', 255)->default('Pcs')->after('stok');
            });
        }
        // nama_stok sudah varchar(255) → tidak diubah

        // ── 6. personal_access_tokens ────────────────────────────────────────
        // token: varchar(64) — ini adalah hash token, TIDAK DIUBAH (intentional)
        // Semua kolom lain sudah varchar(255) atau text → tidak diubah

        // ── 7. users (remember_token) ────────────────────────────────────────
        // remember_token: varchar(100) — ini standar Laravel, TIDAK DIUBAH

        // ── 8. t_kategori ────────────────────────────────────────────────────
        // nama_kategori: sudah varchar(255) → tidak diubah

        // ── 9. qris ──────────────────────────────────────────────────────────
        // image_path: varchar(255), name: varchar(255) → sudah benar
    }

    public function down()
    {
        // Rollback semua perubahan ke kondisi semula

        Schema::table('t_produk', function (Blueprint $table) {
            $table->string('nama_produk', 100)->nullable()->change();
        });

        Schema::table('t_penjualan', function (Blueprint $table) {
            $table->string('nama_pelanggan', 30)->change();
            $table->decimal('total', 60, 0)->change();
        });

        Schema::table('t_riwayat_stok', function (Blueprint $table) {
            $table->integer('total_harga')->nullable()->change();
            $table->string('keterangan', 255)->nullable()->change();
        });

        Schema::table('t_menu_resep', function (Blueprint $table) {
            $table->integer('jumlah')->default(1)->change();
        });
    }
}
