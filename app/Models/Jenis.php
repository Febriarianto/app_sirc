<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jenis extends Model
{
    use HasFactory;
    protected $table = 'jenis';

    protected $fillable = [
        'id',
        'nama',
        'harga_12',
        'harga_24'
    ];
}
