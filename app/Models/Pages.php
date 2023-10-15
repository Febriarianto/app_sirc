<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pages extends Model
{
    use HasFactory;
    protected $table = 'pages';
    public $timestamps = false;


    protected $fillable = [
        'parent_id',
        'title',
        'slug',
        'sort',
    ];

    public function getall()
    {
        return $this->orderBy('sort', 'asc')->where('parent_id', 0)->get();
    }
}
