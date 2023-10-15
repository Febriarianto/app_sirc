<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            [
                'logo' => 'avatar.jpg',
                'favicon' => 'icon.jpg',
                'title' => 'Aplication',
                'deskripsi' => '-',
                'alamat' => '-',
                'maps' => '-',
                'telp' => '-',
                'fax' => '-',
                'email' => '-',
                'facebook' => '-',
                'instagram' => '-',
                'youtube' => '-',
            ]
        ];
        foreach ($settings as $key => $value) {
            $setting = Setting::create($value);
        }
    }
}
