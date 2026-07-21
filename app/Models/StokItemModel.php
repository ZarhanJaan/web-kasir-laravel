<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokItemModel extends Model
{
    use HasFactory;

    protected $table = 't_stok_item';
    protected $primaryKey = 'id_stok';

    protected $fillable = [
        'nama_stok',
        'stok',
        'satuan',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'stok'       => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
