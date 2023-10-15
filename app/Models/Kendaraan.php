<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    use HasFactory;

    protected $table = 'kendaraan';
    protected $fillable = [
        'id',
        'id_pemilik',
        'id_jenis',
        'no_kendaraan',
        'tahun',
        'warna',
        'foto',
    ];
}
