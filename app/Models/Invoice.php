<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'id_transaksi',
        'over_time',
        'biaya',
        'sisa',
        'metode_pelunasan',
        'bukti_pelunasan',
    ];

    public function transaksi()
    {
        // return $this->belongsTo(Transaksi::class, 'transaksi', 'id_transaksi');
        return $this->hasOne(Transaksi::class, 'id', 'id_transaksi');
    }
}
