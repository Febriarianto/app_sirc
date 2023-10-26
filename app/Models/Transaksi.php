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
        'paket',
        'keberangkatan',
        'kepulangan',
        'over_time',
        'biaya',
        'dp',
        'sisa',
        'metode_pelunasan',
        'bukti_pelunasan',
        'metode_dp',
        'bukti_dp',
        'tipe',
        'status',
    ];

    public function penyewa()
    {
        return $this->belongsTo(Penyewa::class, 'id_penyewa');
    }
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan','id');
    }
}
