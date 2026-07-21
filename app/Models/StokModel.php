<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StokModel extends Model
{
    use HasFactory;

    protected $table = 't_riwayat_stok';
    protected $primaryKey = 'id_riwayat';

    /**
     * Attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_produk',
        'id_stok',
        'jenis',
        'jumlah',
        'satuan',
        'harga_beli',
        'keterangan',
        'tanggal',
        'nama_pelanggan',
        'total_harga',
        'created_at',
        'updated_at',
    ];

    /**
     * Attributes type casting.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'jumlah'      => 'integer',
        'harga_beli'  => 'decimal:2',
        'total_harga' => 'decimal:2',
        'tanggal'     => 'date',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    /**
     * Relasi ke ProdukModel
     */
    public function produk()
    {
        return $this->belongsTo(ProdukModel::class, 'id_produk', 'id_produk');
    }

    /* ── Legacy Query Helpers (Backwards Compatibility) ── */

    public function allData()
    {
        return DB::table('t_riwayat_stok')
            ->join('t_produk', 't_riwayat_stok.id_produk', '=', 't_produk.id_produk')
            ->select('t_riwayat_stok.*', 't_produk.nama_produk')
            ->orderBy('t_riwayat_stok.created_at', 'desc')
            ->get();
    }

    public function addData(array $data): void
    {
        if (!isset($data['created_at'])) {
            $data['created_at'] = now();
        }
        if (!isset($data['updated_at'])) {
            $data['updated_at'] = now();
        }
        DB::table('t_riwayat_stok')->insert($data);
    }

    public function filterByDate($tglawal, $tglakhir)
    {
        return DB::table('t_riwayat_stok')
            ->join('t_produk', 't_riwayat_stok.id_produk', '=', 't_produk.id_produk')
            ->select('t_riwayat_stok.*', 't_produk.nama_produk')
            ->whereBetween('t_riwayat_stok.tanggal', [$tglawal, $tglakhir])
            ->orderBy('t_riwayat_stok.created_at', 'desc')
            ->get();
    }
}
