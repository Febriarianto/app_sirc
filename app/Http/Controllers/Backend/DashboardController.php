<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\Penyewa;
use App\Models\Transaksi;

class DashboardController extends Controller
{
    public function index()
    {
        $config['title'] = "Dashboard";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => ""],
        ];

        $countCar = Kendaraan::count();
        $countPenyewa = Penyewa::count();
        $countPemesanan = Transaksi::where('tipe', 'pemesanan')->count();

        $data = [
            'countCar' => $countCar,
            'countPenyewa' => $countPenyewa,
            'countPemesanan' => $countPemesanan,
        ];

        return view('backend.dashboard.index', compact('config', 'data'));
    }
}
