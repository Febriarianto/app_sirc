<?php

namespace App\Models\Permissions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionRole extends Model
{
  use HasFactory;

  protected $table = 'permission_role';
  public $timestamps = false;
  protected $fillable = [
    'permission_id',
    'role_id',
  ];

   public function permissions()
   {
      return $this->belongsTo(Permissions::class, 'permission_id');
   }
}
