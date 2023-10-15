<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    public $table = "setting";

    protected $fillable = [
        'logo',
        'favicon',
        'title',
        'deskripsi',
        'alamat',
        'maps',
        'telp',
        'fax',
        'email',
        'facebook',
        'instagram',
        'youtube',
    ];
}
