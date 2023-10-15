<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'id',
        'id_penyewa',
        'kota_tujuan',
        'id_kendaraan',
        'lama_sewa',
        'keberangkatan',
        'kepulangan',
        'biaya',
        'dp',
        'sisa',
        'kondisi_bbm',
        'dongkrak',
        'ban_cadangan',
        'kelengkapan_lain',
        'jaminan',
    ];
}
