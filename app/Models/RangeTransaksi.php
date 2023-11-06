<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RangeTransaksi extends Model
{
    use HasFactory;

    protected $table = 'range_transaksi';

    public $timestamps = false;

    protected $fillable = [
        'id_transaksi',
        'id_kendaraan',
        'tanggal',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi');
    }
}
