<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProdukModel extends Model
{
    protected $table = 't_produk';
    protected $primaryKey = 'id_produk';
    public $timestamps = false;

    public function allData(){

        return DB::table('t_produk')->get();
    }

    public function addData($data){
        
        DB::table('t_produk')->insert($data);
    }

    public function editData($edit_produk){

        return DB::table('t_produk')->where('id_produk', $edit_produk)->first();
    }

    public function updateData($edit_produk, $data){

        DB::table('t_produk')
        ->where('id_produk', $edit_produk)
        ->update($data);
    }

    public function deleteData($id_produk){

        DB::table('t_produk')
        ->where('id_produk', $id_produk)
        ->delete();
    }
}
