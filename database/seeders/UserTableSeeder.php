<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'Super Admin',
                'username' => 'admin',
                'email' => 'admin@admin.com',
                'role_id' => 1,
                'password' => bcrypt('admin@admin.com'),
                'email_verified_at' => now(),
                'active' => 1,
            ]
        ];
        foreach ($users as $key => $value) {
            $user = User::create($value);
        }
    }
}
