<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{

  /**
   * Auto generated seed file
   *
   * @return void
   */
  public function run()
  {
    $roles = [
      [
        'name' => 'Super Admin',
        'slug' => 'super-admin',
        'dashboard_url' => '/backend/dashboard'
      ],
    ];

    foreach ($roles as $key => $value) {
      $role = Role::create($value);
    }
  }
}
