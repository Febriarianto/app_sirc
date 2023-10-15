<?php

namespace App\Models\Permissions;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuManagerRole extends Model
{
    use HasFactory;
  protected $table = 'menu_manager_role';
  public $timestamps = false;
  protected $fillable = [
    'menu_manager_id',
    'role_id',
  ];

   public function roles()
   {
      return $this->belongsTo(Role::class, 'role_id');
   }

   public function menu_manager()
   {
      return $this->belongsTo(MenuManager::class, 'menu_manager_id');
   }

   public function getall($id)
   {
      return $this->with([
         'roles',
         'menu_manager'
      ])->where("role_id", $id)->get();
   }
}
