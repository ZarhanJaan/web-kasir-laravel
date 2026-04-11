<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuDetailModel extends Model
{
    protected $table = 't_menu_detail';
    public $timestamps = false;
    
    protected $fillable = ['id_menu', 'id_produk', 'jumlah_dipakai'];

    public function menu()
    {
        return $this->belongsTo(MenuModel::class, 'id_menu', 'id_menu');
    }
}
