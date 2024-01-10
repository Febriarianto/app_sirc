<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Transaksi;
use Yajra\DataTables\DataTables;
use App\Traits\ResponseStatus;
use Carbon\Carbon;

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

        $tAwal = $request['tAwal'] ?? Carbon::now()->format('Y-m-d');
        $tAhir = $request['tAhir'] ?? Carbon::now()->format('Y-m-d');
        if ($request->ajax()) {
            $data = Transaksi::where('id_kendaraan', $request['kendaraan'])
                ->select('transaksi.id', 'penyewa.nama', 'transaksi.keberangkatan', 'transaksi.kepulangan', 'transaksi.biaya', 'transaksi.kepulangan_time', 'transaksi.keberangkatan_time')
                ->leftJoin('penyewa', 'transaksi.id_penyewa', '=', 'penyewa.id')
                // ->where('keberangkatan', 'LIKE', '%' . $request['bulan'] . '%')
                ->whereBetween('keberangkatan', [$tAwal, $tAhir])
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
            $data = Transaksi::select('transaksi.id', 'transaksi.lama_sewa', 'kendaraan.no_kendaraan as kendaraan', 'penyewa.nama as penyewa', 'transaksi.keberangkatan', 'transaksi.kepulangan', 'transaksi.keberangkatan_time', 'transaksi.kepulangan_time', 'transaksi.keterangan', 'transaksi.sisa', 'transaksi.biaya', 'transaksi.status', 'pembayaran.tipe', 'pembayaran.metode', 'pembayaran.nominal', 'pembayaran.created_at', 'pembayaran.penerima')
                ->leftJoin('kendaraan', 'transaksi.id_kendaraan', '=', 'kendaraan.id')
                ->leftJoin('penyewa', 'transaksi.id_penyewa', '=', 'penyewa.id')
                ->leftJoin('pembayaran', 'transaksi.id', '=', 'pembayaran.id_transaksi')
                ->where('transaksi.kepulangan', '=', $request['tgl'])
                ->where('transaksi.status', '=', 'selesai')
                ->orWhereDate('pembayaran.created_at', $request['tgl'])
                ->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->make();
        }
        return view('backend.laporan.harian', compact('config'));
    }
}
