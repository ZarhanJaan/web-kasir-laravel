<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProdukModel extends Model
{
    use HasFactory;

    protected $table = 't_produk';
    protected $primaryKey = 'id_produk';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // Dikendalikan manual / via DB default

    /**
     * Attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_produk',
        'nama_produk',
        'stok',
        'harga_beli',
        'harga_jual',
        'kategori',
        'created_at',
        'updated_at',
    ];

    /**
     * Attributes type casting.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'stok'       => 'integer',
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Normalisasi ID produk ke integer string (misal: "0001" -> "1")
     */
    public static function normalizeId($id): string
    {
        return (string) (int) $id;
    }

    /**
     * Menentukan prefix ID berdasarkan nama kategori:
     * - Makanan => 1 (1001, 1002, dst.)
     * - Minuman => 2 (2001, 2002, dst.)
     * - Lainnya => Dinamis dari t_kategori (3, 4, dst.)
     */
    public static function getCategoryPrefix(string $kategori): string
    {
        $kat = strtolower(trim($kategori));
        if ($kat === 'makanan') {
            return '1';
        }
        if ($kat === 'minuman') {
            return '2';
        }

        $prefixMap = ['makanan' => '1', 'minuman' => '2'];
        $nextPrefix = 3;

        if (Schema::hasTable('t_kategori')) {
            $categories = DB::table('t_kategori')->orderBy('id_kategori')->pluck('nama_kategori')->toArray();
            foreach ($categories as $c) {
                $cKey = strtolower(trim($c));
                if (!isset($prefixMap[$cKey])) {
                    $prefixMap[$cKey] = (string) $nextPrefix++;
                }
            }
        }

        return $prefixMap[$kat] ?? '3';
    }

    /**
     * Dapatkan ID unik berikutnya untuk kategori tertentu.
     */
    public static function getNextIdForCategory(string $kategori): string
    {
        $prefix = self::getCategoryPrefix($kategori);
        $minVal = (int) ($prefix . '001');
        $maxVal = (int) ($prefix . '999');

        $maxId = DB::table('t_produk')
            ->whereRaw('CAST(id_produk AS UNSIGNED) >= ? AND CAST(id_produk AS UNSIGNED) <= ?', [$minVal, $maxVal])
            ->max(DB::raw('CAST(id_produk AS UNSIGNED)'));

        if ($maxId) {
            return (string) ($maxId + 1);
        }

        return (string) $minVal;
    }

    /* ── Legacy Query Helper (Backwards Compatibility) ── */

    public function allData()
    {
        return DB::table('t_produk')->get();
    }

    public function addData(array $data): void
    {
        DB::table('t_produk')->insert($data);
    }

    public function editData($edit_produk)
    {
        return DB::table('t_produk')->where('id_produk', $edit_produk)->first();
    }

    public function updateData($edit_produk, array $data): void
    {
        DB::table('t_produk')
            ->where('id_produk', $edit_produk)
            ->update($data);
    }

    public function deleteData($id_produk): void
    {
        DB::table('t_produk')
            ->where('id_produk', $id_produk)
            ->delete();
    }
}
