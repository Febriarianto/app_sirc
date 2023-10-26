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

    public function pemilik()
    {
        return $this->belongsTo(Pemilik::class, 'id_pemilik');
    }
    public function jenis()
    {
        return $this->belongsTo(Jenis::class, 'id_jenis');
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'id_kendaraan', 'id');
    }
}
