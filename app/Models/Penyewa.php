<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penyewa extends Model
{
    use HasFactory;
    protected $table = 'penyewa';

    protected $fillable = [
        'id',
        'nik',
        'nama',
        'no_hp',
        'alamat',
        'ktp',
        'kk',
        'foto',
        'referral_id',
        'status',
    ];
    public function referral()
    {
        return $this->belongsTo(Referral::class, 'referral_id');
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'id_penyewa');
    }
}
