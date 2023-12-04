<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Kendaraan;
use App\Models\Penyewa;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Traits\ResponseStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

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
        $data = Transaksi::selectRaw('count(*) as nilai, MONTH(keberangkatan) as bulan')
            ->whereYear('keberangkatan', $year)
            ->where('tipe', '=', 'sewa')
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


    public function checkin(Request $request)
    {
        $config['title'] = "Check In";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Check In"],
        ];

        if ($request->ajax()) {
            $data = Transaksi::select(
                'transaksi.id',
                'kendaraan.no_kendaraan as kendaraan',
                'penyewa.nama as penyewa',
                'transaksi.keberangkatan',
                'kendaraan.barcode',
            )
                ->leftJoin('kendaraan', 'transaksi.id_kendaraan', '=', 'kendaraan.id')
                ->leftJoin('penyewa', 'transaksi.id_penyewa', '=', 'penyewa.id')
                ->where('kendaraan.barcode', $request->kode)
                ->where('transaksi.status', '=', 'proses')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a class="btn btn-info btn-info" href="' . route('penyewaan.show', $row->id) . '">Check IN</a>';
                    return $actionBtn;
                })
                ->make();
        }

        return view('backend.dashboard.checkin', compact('config'));
    }
}
