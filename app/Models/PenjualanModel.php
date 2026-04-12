<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PenjualanModel extends Model
{
    protected $table = 't_penjualan';

    public function allData(){

        return DB::table('t_penjualan')->get();
    }

    public function addData($data){
        $data['created_at'] = now();
        $data['updated_at'] = now();
        DB::table('t_penjualan')->insert($data);
    }

    public function editData($edit_penjualan){

        return DB::table('t_penjualan')->where('id_penjualan', $edit_penjualan)->first();
    }

    public function updateData($edit_penjualan, $data){

        DB::table('t_penjualan')
        ->where('id_penjualan', $edit_penjualan)
        ->update($data);
    }

    public function deleteData($id_penjualan){

        DB::table('t_penjualan')
        ->where('id_penjualan', $id_penjualan)
        ->delete();
    }
}
