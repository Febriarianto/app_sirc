<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
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

    public function graph()
    {
        $year = date('Y');
        $data = Invoice::selectRaw('count(*) as nilai, MONTH(keberangkatan) as bulan')
            ->whereYear('keberangkatan', $year)
            ->groupByRaw('MONTH(keberangkatan)')
            ->get();


        $bulan = [];
        $countBulan = [];

        foreach ($data as $key => $value) {
            $bulan[(int)$data[$key]['bulan']] = $data[$key]['nilai'];
        }

        for ($i = 1; $i <= 12; $i++) {
            if (!empty($bulan[$i])) {
                $countBulan[$i] = $bulan[$i];
            } else {
                $countBulan[$i] = 0;
            }
        }
        $response = response()->json($countBulan);

        return $response;
    }
}
