<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    $this->call([
      RoleTableSeeder::class,
      UserTableSeeder::class,
      SettingTableSeeder::class,
    ]);
    $this->call(MenuManagersTableSeeder::class);
    $this->call(MenuManagerRoleTableSeeder::class);
      $this->call(PermissionsTableSeeder::class);
        $this->call(PermissionRoleTableSeeder::class);
    }
}
