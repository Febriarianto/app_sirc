<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Transaksi;
use Yajra\DataTables\DataTables;
use App\Traits\ResponseStatus;

class LaporanController extends Controller
{
    use ResponseStatus;

    function __construct()
    {
        $this->middleware('can:laporan-bulanan-list', ['only' => ['bulanan_index', 'bulanan_show']]);
        $this->middleware('can:laporan-referral-list', ['only' => ['referral_index', 'referral_show']]);
    }

    public function bulanan_index(Request $request)
    {
        $config['title'] = "Laporan Bulanan";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Laporan Bulanan"],
        ];
        if ($request->ajax()) {
            $data = Transaksi::where('id_kendaraan', $request['kendaraan'])
                ->select('transaksi.id', 'penyewa.nama', 'transaksi.keberangkatan', 'transaksi.kepulangan', 'transaksi.biaya', 'transaksi.kepulangan_time', 'transaksi.keberangkatan_time')
                ->leftJoin('penyewa', 'transaksi.id_penyewa', '=', 'penyewa.id')
                ->where('keberangkatan', 'LIKE', '%' . $request['bulan'] . '%')
                ->where('transaksi.status', '=', 'selesai');
            return DataTables::of($data)
                ->addIndexColumn()
                ->make();
        }
        return view('backend.laporan.bulanan', compact('config'));
    }

    public function bulanan_show()
    {
    }

    public function referral_index(Request $request)
    {
        $config['title'] = "Laporan Referral";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Laporan Referral"],
        ];
        if ($request->ajax()) {
            $data = Transaksi::select('transaksi.id', 'penyewa.nama', 'transaksi.keberangkatan', 'transaksi.kepulangan', 'transaksi.biaya')
                ->leftJoin('penyewa', 'transaksi.id_penyewa', '=', 'penyewa.id')
                ->selectRaw('biaya * 0.1 as komisi')
                ->where('penyewa.referral_id', '=', $request['referral'])
                ->where('keberangkatan', 'LIKE', '%' . $request['bulan'] . '%')
                ->where('transaksi.status', '=', 'selesai');
            return DataTables::of($data)
                ->addIndexColumn()
                ->make();
        }

        return view('backend.laporan.referral', compact('config'));
    }

    public function referral_show()
    {
    }

    public function harian_index(Request $request)
    {
        $config['title'] = "Laporan Harian";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Laporan Harian"],
        ];
        if ($request->ajax()) {
            $data = Transaksi::select('transaksi.id', 'transaksi.paket', 'kendaraan.no_kendaraan as kendaraan', 'penyewa.nama as penyewa', 'transaksi.lama_sewa', 'transaksi.keberangkatan', 'transaksi.kepulangan', 'transaksi.keberangkatan_time', 'transaksi.kepulangan_time', 'transaksi.dp', 'transaksi.keterangan', 'transaksi.sisa', 'transaksi.metode_pelunasan', 'transaksi.status')
                ->selectRaw('transaksi.dp + transaksi.sisa as total')
                ->leftJoin('kendaraan', 'transaksi.id_kendaraan', '=', 'kendaraan.id')
                ->leftJoin('penyewa', 'transaksi.id_penyewa', '=', 'penyewa.id')
                ->where('transaksi.status', '=', 'selesai')
                ->where('transaksi.kepulangan', $request->tgl)
                ->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->make();
        }
        return view('backend.laporan.harian', compact('config'));
    }
}
