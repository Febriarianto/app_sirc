<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'nama',
        'alamat',
        'no_hp',
        'no_rekening',
    ];

    public function referral()
    {
        return $this->belongsTo(Referral::class);
    }
}
