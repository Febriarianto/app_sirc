<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MenuManagersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('menu_managers')->delete();
        
        \DB::table('menu_managers')->insert(array (
            0 => 
            array (
                'id' => 1,
                'parent_id' => 0,
                'title' => 'Dashboard',
                'slug' => '',
                'path_url' => '/backend/dashboard',
                'icon' => 'fas fa-tachometer-alt',
                'type' => 'module',
                'position' => NULL,
                'sort' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'parent_id' => 0,
                'title' => 'Setting',
                'slug' => NULL,
                'path_url' => NULL,
                'icon' => 'fas fa-cogs',
                'type' => 'static',
                'position' => NULL,
                'sort' => 2,
            ),
            2 => 
            array (
                'id' => 8,
                'parent_id' => 2,
                'title' => 'Users',
                'slug' => 'users',
                'path_url' => '/backend/users',
                'icon' => 'fas fa-users',
                'type' => 'module',
                'position' => NULL,
                'sort' => 1,
            ),
            3 => 
            array (
                'id' => 9,
                'parent_id' => 2,
                'title' => 'Roles',
                'slug' => 'roles',
                'path_url' => '/backend/roles',
                'icon' => 'fas fa-user-tag',
                'type' => 'module',
                'position' => NULL,
                'sort' => 2,
            ),
            4 => 
            array (
                'id' => 10,
                'parent_id' => 2,
                'title' => 'Menu Manager',
                'slug' => 'menu-manager',
                'path_url' => '/backend/menu-manager',
                'icon' => 'fas fa-bars',
                'type' => 'module',
                'position' => NULL,
                'sort' => 3,
            ),
            5 => 
            array (
                'id' => 11,
                'parent_id' => 2,
                'title' => 'Web',
                'slug' => 'setting',
                'path_url' => '/backend/setting',
                'icon' => 'far fa-window-restore',
                'type' => 'module',
                'position' => NULL,
                'sort' => 4,
            ),
        ));
        
        
    }
}