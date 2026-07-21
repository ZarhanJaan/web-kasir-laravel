<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PenjualanModel extends Model
{
    use HasFactory;

    protected $table = 't_penjualan';
    protected $primaryKey = 'id_penjualan';

    /**
     * Attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tanggal',
        'nama_pelanggan',
        'jumlah_barang',
        'id_produk',
        'total',
        'metode_pembayaran',
        'created_at',
        'updated_at',
    ];

    /**
     * Attributes type casting.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal'    => 'date',
        'total'      => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ── Legacy Query Helpers (Backwards Compatibility) ── */

    public function allData()
    {
        return DB::table('t_penjualan')->get();
    }

    public function addData(array $data): void
    {
        if (!isset($data['created_at'])) {
            $data['created_at'] = now();
        }
        if (!isset($data['updated_at'])) {
            $data['updated_at'] = now();
        }
        DB::table('t_penjualan')->insert($data);
    }

    public function editData($edit_penjualan)
    {
        return DB::table('t_penjualan')->where('id_penjualan', $edit_penjualan)->first();
    }

    public function updateData($edit_penjualan, array $data): void
    {
        if (!isset($data['updated_at'])) {
            $data['updated_at'] = now();
        }
        DB::table('t_penjualan')
            ->where('id_penjualan', $edit_penjualan)
            ->update($data);
    }

    public function deleteData($id_penjualan): void
    {
        DB::table('t_penjualan')
            ->where('id_penjualan', $id_penjualan)
            ->delete();
    }
}
