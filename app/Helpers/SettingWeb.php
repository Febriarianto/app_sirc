<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use App\Models\Setting;

class SettingWeb
{
    public static function get_setting()
    {
        $data = Setting::first();
        return $data;
    }
}
