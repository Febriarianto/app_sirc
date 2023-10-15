<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MenuManagerRoleTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('menu_manager_role')->delete();
        
        \DB::table('menu_manager_role')->insert(array (
            0 => 
            array (
                'id' => 4,
                'menu_manager_id' => 1,
                'role_id' => 1,
            ),
            1 => 
            array (
                'id' => 5,
                'menu_manager_id' => 8,
                'role_id' => 1,
            ),
            2 => 
            array (
                'id' => 6,
                'menu_manager_id' => 9,
                'role_id' => 1,
            ),
            3 => 
            array (
                'id' => 7,
                'menu_manager_id' => 10,
                'role_id' => 1,
            ),
            4 => 
            array (
                'id' => 8,
                'menu_manager_id' => 11,
                'role_id' => 1,
            ),
        ));
        
        
    }
}