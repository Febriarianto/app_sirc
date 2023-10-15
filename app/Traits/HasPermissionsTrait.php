<?php

namespace App\Traits;

use App\Models\Permissions\PermissionRole;
use App\Models\Permissions\Permissions;
use App\Models\Role;

trait HasPermissionsTrait
{

   public function givePermissionsTo(...$permissions)
   {
      $permissions = $this->getAllPermissions($permissions);
      if ($permissions === null) {
         return $this;
      }
      $this->permissions()->saveMany($permissions);
      return $this;
   }

   public function withdrawPermissionsTo(...$permissions)
   {
      $permissions = $this->getAllPermissions($permissions);
      $this->permissions()->detach($permissions);
      return $this;
   }

   public function refreshPermissions(...$permissions)
   {
      $this->permissions()->detach();
      return $this->givePermissionsTo($permissions);
   }

   public function hasPermissionTo($permission)
   {
      return $this->hasPermissionThroughRole($permission) || $this->hasPermission($permission);
   }

   public function hasPermissionThroughRole($permission)
   {
      return false;
   }

   public function hasRole(...$roles)
   {
      foreach ($roles as $role) {
         if ($this->roles['slug'] == $role) {
            return true;
         }
      }
      return false;
   }

   public function roles()
   {
      return $this->belongsTo(Role::class, 'role_id');
   }

//   public function permissions_role()
//   {
//      return $this->belongsTo(PermissionRole::class);
//   }
//
//   public function permissions()
//   {
//      return $this->belongsTo(Permissions::class);
//   }

   protected function hasPermission($permission)
   {
      return (bool)$this->roles->permissions_role->where('permission_id', $permission->id)->count();
   }

   protected function getAllPermissions(array $permissions)
   {
      return Permissions::whereIn('slug', $permissions)->get();
   }
}
