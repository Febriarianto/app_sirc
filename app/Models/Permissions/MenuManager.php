<?php

namespace App\Models\Permissions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MenuManager extends Model
{
  use HasFactory;
  protected $table = 'menu_managers';
  public $timestamps = false;

  protected $fillable = [
    'parent_id',
    'title',
    'slug',
    'path_url',
    'icon',
    'type',
    'position',
    'sort',
  ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($menumanagers) {
           if($menumanagers->type == 'module') {
              $menumanagers->slug = Str::slug($menumanagers->slug);
           }
        });

        static::updating(function ($menumanagers) {
           if($menumanagers->type == 'module') {
              $menumanagers->slug = Str::slug($menumanagers->slug);
           }
        });
    }

   public function getall()
   {
      return $this->orderBy('sort', 'asc')->where('parent_id', 0)->get();
   }

   public function permissions()
   {
      return $this->hasMany(Permissions::class, 'menu_manager_id');
   }

   public function menu_manager_role()
   {
      return $this->hasMany(MenuManagerRole::class, 'menu_manager_id');
   }

   public function get_menu_role($role)
   {
      return $this
         ->with(['menu_manager_role' => function($query) use ($role) {
         $query->where('role_id', $role);
         }])
         ->whereHas('menu_manager_role', function ($query) use ($role) {
            $query->where('role_id', $role);
         })->get();
   }
}
