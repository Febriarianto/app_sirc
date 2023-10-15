<?php

namespace App\Models;

use App\Models\Permissions\MenuManagerRole;
use App\Models\Permissions\PermissionRole;
use App\Models\Permissions\Permissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Role extends Model
{
  use HasFactory;

  public $timestamps = false;
  protected $primaryKey = 'id';

  protected $fillable = [
    'id',
    'name',
    'slug',
    'dashboard_url',
  ];

  protected static function boot()
  {
    parent::boot();
    static::creating(function ($roles) {
      $roles->slug = Str::slug($roles->name);
    });

    static::updating(function ($roles) {
      $roles->slug = Str::slug($roles->name);
    });

  }

  public function permissions_role()
  {
    return $this->hasMany(PermissionRole::class, 'role_id');
  }

  public function permissions()
  {
    return $this->permissions_role->belongsTo(Permissions::class, 'permission_id');
  }

  public function menu_manager()
  {
    return $this->hasMany(MenuManagerRole::class, 'role_id');
  }

  public function users()
  {
    return $this->belongsToMany(User::class);
  }
}
