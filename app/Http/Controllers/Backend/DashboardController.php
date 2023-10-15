<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Puskesmas;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $config['title'] = "Dashboard";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => ""],
        ];

        $countAdmin = User::where('role_id', '1')->count();
        $countOperator = User::where('role_id', '2')->count();

        $data = [
            'countAdmin' => $countAdmin,
            'countOperator' => $countOperator,
        ];

        return view('backend.dashboard.index', compact('config', 'data'));
    }
}
