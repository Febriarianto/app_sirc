<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HargaKendaraan extends Model
{
    use HasFactory;
    protected $table = 'harga_kendaraan';

    protected $fillable = [
        'id',
        'id_kendaraan',
        'id_harga',
    ];
}
