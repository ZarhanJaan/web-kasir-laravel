<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StokModel extends Model
{
    protected $table = 't_riwayat_stok';
    protected $primaryKey = 'id_riwayat';

    public function allData()
    {
        return DB::table('t_riwayat_stok')
            ->join('t_produk', 't_riwayat_stok.id_produk', '=', 't_produk.id_produk')
            ->select('t_riwayat_stok.*', 't_produk.nama_produk')
            ->orderBy('t_riwayat_stok.created_at', 'desc')
            ->get();
    }

    public function addData($data)
    {
        $creator = auth()->user();
        if ($creator) {
            $data['created_by_id'] = $creator->id;
            $data['created_by_name'] = $creator->name;
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
