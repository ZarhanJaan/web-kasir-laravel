<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuResepModel extends Model
{
    use HasFactory;

    protected $table = 't_menu_resep';
    protected $primaryKey = 'id_resep';

    protected $fillable = [
        'id_menu',
        'id_stok',
        'jumlah',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'jumlah'     => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function produk()
    {
        return $this->belongsTo(ProdukModel::class, 'id_menu', 'id_produk');
    }

    public function stokItem()
    {
        return $this->belongsTo(StokItemModel::class, 'id_stok', 'id_stok');
    }
}
