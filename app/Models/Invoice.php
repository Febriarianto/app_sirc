<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'id_penyewa',
        'id_kendaraan',
        'kota_tujuan',
        'keberangkatan',
        'kepulangan',
        'paket',
        'harga_paket',
        'lama_sewa',
        'biaya_overtime',
        'total_biaya',
        'dp',
        'bukti_dp',
        'metode_pelunasan',
        'bukti_pelunasan',
    ];

    public function penyewa()
    {
        return $this->belongsTo(Penyewa::class, 'id_penyewa');
    }
    
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan');
    }
}
