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
        'dp',
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
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan', 'id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'id_transaksi', 'id');
    }

    public function range_transaksi()
    {
        return $this->belongsToMany(RangeTransaksi::class);
    }
}
