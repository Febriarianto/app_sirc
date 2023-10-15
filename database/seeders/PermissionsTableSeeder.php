<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('permissions')->delete();
        
        \DB::table('permissions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'menu_manager_id' => 1,
                'name' => 'Dashboard List',
                'slug' => 'list',
            ),
            1 => 
            array (
                'id' => 2,
                'menu_manager_id' => 1,
                'name' => 'Dashboard Create',
                'slug' => 'create',
            ),
            2 => 
            array (
                'id' => 3,
                'menu_manager_id' => 1,
                'name' => 'Dashboard Edit',
                'slug' => 'edit',
            ),
            3 => 
            array (
                'id' => 4,
                'menu_manager_id' => 1,
                'name' => 'Dashboard Delete',
                'slug' => 'delete',
            ),
            4 => 
            array (
                'id' => 5,
                'menu_manager_id' => 8,
                'name' => 'Users List',
                'slug' => 'users-list',
            ),
            5 => 
            array (
                'id' => 6,
                'menu_manager_id' => 8,
                'name' => 'Users Create',
                'slug' => 'users-create',
            ),
            6 => 
            array (
                'id' => 7,
                'menu_manager_id' => 8,
                'name' => 'Users Edit',
                'slug' => 'users-edit',
            ),
            7 => 
            array (
                'id' => 8,
                'menu_manager_id' => 8,
                'name' => 'Users Delete',
                'slug' => 'users-delete',
            ),
            8 => 
            array (
                'id' => 9,
                'menu_manager_id' => 9,
                'name' => 'Roles List',
                'slug' => 'roles-list',
            ),
            9 => 
            array (
                'id' => 10,
                'menu_manager_id' => 9,
                'name' => 'Roles Create',
                'slug' => 'roles-create',
            ),
            10 => 
            array (
                'id' => 11,
                'menu_manager_id' => 9,
                'name' => 'Roles Edit',
                'slug' => 'roles-edit',
            ),
            11 => 
            array (
                'id' => 12,
                'menu_manager_id' => 9,
                'name' => 'Roles Delete',
                'slug' => 'roles-delete',
            ),
            12 => 
            array (
                'id' => 13,
                'menu_manager_id' => 10,
                'name' => 'Menu Manager List',
                'slug' => 'menu-manager-list',
            ),
            13 => 
            array (
                'id' => 14,
                'menu_manager_id' => 10,
                'name' => 'Menu Manager Create',
                'slug' => 'menu-manager-create',
            ),
            14 => 
            array (
                'id' => 15,
                'menu_manager_id' => 10,
                'name' => 'Menu Manager Edit',
                'slug' => 'menu-manager-edit',
            ),
            15 => 
            array (
                'id' => 16,
                'menu_manager_id' => 10,
                'name' => 'Menu Manager Delete',
                'slug' => 'menu-manager-delete',
            ),
            16 => 
            array (
                'id' => 17,
                'menu_manager_id' => 11,
                'name' => 'Web List',
                'slug' => 'setting-list',
            ),
            17 => 
            array (
                'id' => 18,
                'menu_manager_id' => 11,
                'name' => 'Web Create',
                'slug' => 'setting-create',
            ),
            18 => 
            array (
                'id' => 19,
                'menu_manager_id' => 11,
                'name' => 'Web Edit',
                'slug' => 'setting-edit',
            ),
            19 => 
            array (
                'id' => 20,
                'menu_manager_id' => 11,
                'name' => 'Web Delete',
                'slug' => 'setting-delete',
            ),
        ));
        
        
    }
}