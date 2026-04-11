<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuModel extends Model
{
    protected $table = 't_menu';
    protected $primaryKey = 'id_menu';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = ['id_menu', 'nama_menu', 'harga_menu'];

    public function details()
    {
        return $this->hasMany(MenuDetailModel::class, 'id_menu', 'id_menu');
    }
}
