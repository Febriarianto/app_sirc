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
        'no_inv',
        'id_penyewa',
        'id_kendaraan',
        'kota_tujuan',
        'lama_sewa',
        'durasi',
        'paket',
        'keberangkatan',
        'estimasi_time',
        'keberangkatan_time',
        'kepulangan',
        'kepulangan_time',
        'tipe',
        'status',
        'jaminan',
        'harga_sewa',
        'diskon',
        'over_time',
        'biaya',
        'sisa',
        'keterangan',
    ];

    public function penyewa()
    {
        return $this->belongsTo(Penyewa::class, 'id_penyewa');
    }
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan', 'id');
    }

    public function pembayar()
    {
        return $this->belongsToMany(Pembayaran::class);
    }
}
