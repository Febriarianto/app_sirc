<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Kendaraan;
use App\Models\HargaKendaraan;
use App\Models\Penyewa;
use App\Models\Transaksi;
use App\Models\RangeTransaksi;
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

        $kendaraan = Kendaraan::select('kendaraan.id')
            ->selectRaw('(select count(id) from transaksi where id_kendaraan = kendaraan.id and transaksi.status = "proses") as s ')
            ->where('kendaraan.status', '=', 'aktif')
            ->leftJoin('jenis', 'jenis.id', '=', 'kendaraan.id_jenis')
            ->get();

        $countAvail = 0;
        $countNotAvail = 0;

        foreach ($kendaraan as $key => $value) {
            if ($value->s == 0) {
                $countAvail = $countAvail + 1;
            } else {
                $countNotAvail = $countNotAvail + 1;
            }
        }
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

    public function prosesCheckin()
    {
        $id = $_GET['id'];

        $data = Transaksi::with('penyewa', 'kendaraan')->where('id', $id)->first();

        $kepulangan = Carbon::now();
        $kepulangan_time = Carbon::now();

        // if ($data->sisa !== 0) {
        //     $ket = "belum lunas";
        // } else {
        //     $ket = "lunas";
        // }

        $start = Carbon::parse($data->keberangkatan . $data->keberangkatan_time);
        $end =  Carbon::parse();
        $duration = $end->diff($start);
        $hari = $end->diffInDays($start);
        $jam = $duration->format('%H');

        $d = $hari . " Hari " . $jam . " Jam";

        $toleransi = 1;

        $get_harga_harian = HargaKendaraan::SELECT('harga_kendaraan.id', 'harga.nilai', 'harga.nominal')
            ->JOIN('harga', 'harga.id', '=', 'harga_kendaraan.id_harga')
            ->WHERE('harga_kendaraan.id_kendaraan', $data->id_kendaraan)
            ->WHERE('harga.nilai', '=', 24)
            ->first();

        if ($hari == 0 && $jam == 0 | $hari == 0 && $jam - $toleransi == 0) {
            $get_harga_sql = HargaKendaraan::SELECT('harga_kendaraan.id', 'harga.nilai', 'harga.nominal')
                ->JOIN('harga', 'harga.id', '=', 'harga_kendaraan.id_harga')
                ->WHERE('harga_kendaraan.id_kendaraan', $data->id_kendaraan)
                ->WHERE('harga.nilai', '>=', $jam)
                ->ORDERBY('harga.nilai', 'ASC')
                ->first();
            $get_harga_jam = $get_harga_sql->nominal;
        } elseif ($hari !== 0 && $jam == 0 | $hari !== 0 && $jam - $toleransi == 0) {
            $get_harga_jam = 0;
        } else {
            $get_harga_sql = HargaKendaraan::SELECT('harga_kendaraan.id', 'harga.nilai', 'harga.nominal')
                ->JOIN('harga', 'harga.id', '=', 'harga_kendaraan.id_harga')
                ->WHERE('harga_kendaraan.id_kendaraan', $data->id_kendaraan)
                ->WHERE('harga.nilai', '>=', $jam - $toleransi)
                ->ORDERBY('harga.nilai', 'ASC')
                ->first();
            $get_harga_jam = $get_harga_sql->nominal;
        }

        $harga = $hari * $get_harga_harian->nominal + $get_harga_jam * 1;

        // dd($harga, $d);

        $dataTgl = RangeTransaksi::where('id_transaksi', $id)->delete();

        $data->update([
            'durasi' => $d,
            'kepulangan' => $kepulangan,
            'kepulangan_time' => $kepulangan_time,
            'harga_sewa' => $harga,
            'status' => 'selesai',
        ]);

        $response = response()->json(['message' => 'success']);
        return $response;
    }
}
