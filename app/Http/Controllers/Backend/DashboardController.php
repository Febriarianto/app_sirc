<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Kendaraan;
use App\Models\Penyewa;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Traits\ResponseStatus;
use Carbon\Carbon;
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

        $tanggal = Carbon::now()->format('Y-m-d');

        $kendaraan = DB::table('kendaraan')
            ->selectRaw("SUM(CASE WHEN `range_transaksi`.`tanggal` IS null THEN 1 ELSE 0 END)
            as 'ada', COUNT(`range_transaksi`.`tanggal`) as 'tidakAda'")
            ->where('kendaraan.status', '=', 'aktif')
            ->leftJoin('jenis', 'jenis.id', '=', 'kendaraan.id_jenis')
            ->leftJoin('range_transaksi', function ($join) use ($tanggal) {
                $join->on('kendaraan.id', '=', 'range_transaksi.id_kendaraan')
                    ->where('range_transaksi.tanggal', $tanggal,);
            })
            ->get();

        $countAvail = $kendaraan[0]->ada;
        $countNotAvail = $kendaraan[0]->tidakAda;
        // $countCar = Kendaraan::count();
        $countPenyewa = Penyewa::count();
        $countPemesanan = Transaksi::where('tipe', 'pesan')->count();

        $data = [
            // 'countCar' => $countCar,
            'countAvail' => $countAvail,
            'countNotAvail' => $countNotAvail,
            'countPenyewa' => $countPenyewa,
            'countPemesanan' => $countPemesanan,
        ];

        return view('backend.dashboard.index', compact('config', 'data'));
    }

    public function graph()
    {
        $nopol = $_GET['nopol'];
        $year = date('Y');
        if ($nopol !== "") {
            $data = Transaksi::selectRaw('count(*) as nilai, MONTH(keberangkatan) as bulan')
                ->whereYear('keberangkatan', $year)
                ->where('tipe', '=', 'sewa')
                ->where('id_kendaraan', $nopol)
                ->groupByRaw('MONTH(keberangkatan)')
                ->get();
        } else {
            $data = Transaksi::selectRaw('count(*) as nilai, MONTH(keberangkatan) as bulan')
                ->whereYear('keberangkatan', $year)
                ->where('tipe', '=', 'sewa')
                ->groupByRaw('MONTH(keberangkatan)')
                ->get();
        }

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
                'transaksi.kepulangan',
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
                    $actionBtn = '<a class="btn btn-info btn-checkin"  href="#" data-id="' . $row->id . '">Check IN</a>';
                    return $actionBtn;
                })
                ->make();
        }

        return view('backend.dashboard.checkin', compact('config'));
    }
}
