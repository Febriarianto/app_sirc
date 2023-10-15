<?php

namespace App\Models\Permissions;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Permissions extends Model
{
  use HasFactory;

  protected $table = 'permissions';
  public $timestamps = false;

  protected $fillable = [
    'menu_manager_id',
    'name',
    'slug',
  ];

  protected static function boot()
  {
    parent::boot();
    static::creating(function ($permission) {
      $permission->slug = Str::slug($permission->slug);
    });

    static::updating(function ($permission) {
      $permission->slug = Str::slug($permission->slug);
    });
  }

   public function roles()
   {
      return $this->hasMany(PermissionRole::class,'permission_id');
   }
}
