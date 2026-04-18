<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qris extends Model
{
    use HasFactory;

    protected $table = 'qris';

    protected $fillable = [
        'image_path',
        'name',
    ];
}
