<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Pembayaran;
use App\Models\Kendaraan;
use App\Models\Transaksi;
use Yajra\DataTables\DataTables;
use App\Traits\ResponseStatus;
use Carbon\Carbon;

class LaporanController extends Controller
{
    use ResponseStatus;

    function __construct()
    {
        $this->middleware('can:laporan-harian-list', ['only' => ['harian_index', 'judul']]);
        $this->middleware('can:laporan-bulanan-list', ['only' => ['bulanan_index', 'judul']]);
        $this->middleware('can:laporan-omset-list', ['only' => ['omset_index', 'judul']]);
        $this->middleware('can:laporan-referral-list', ['only' => ['referral_index', 'judul']]);
    }

    public function judul(Request $request)
    {
        if ($request->ajax()) {
            $id = $_GET['id'];
            $data = Kendaraan::select('kendaraan.no_kendaraan', 'jenis.nama as jenis', 'pemilik.nama as pemilik', 'kendaraan.warna')
                ->join('jenis', 'jenis.id', '=', 'kendaraan.id_jenis')
                ->join('pemilik', 'pemilik.id', '=', 'kendaraan.id_pemilik')
                ->where('kendaraan.id', $id)
                ->first();
            return response($data);
        }
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
                ->whereBetween('kepulangan', [$tAwal, $tAhir])
                ->where('transaksi.status', '=', 'selesai');
            return DataTables::of($data)
                ->addIndexColumn()
                ->make();
        }
        return view('backend.laporan.bulanan', compact('config'));
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

    public function harian_index(Request $request)
    {
        $config['title'] = "Laporan Harian";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Laporan Harian"],
        ];

        if ($request->ajax()) {
            $tgl = $_GET['tgl'];
            $data = Transaksi::select(
                'transaksi.id',
                'transaksi.id_kendaraan',
                'transaksi.id_penyewa',
                'penyewa.nama',
                'transaksi.lama_sewa',
                'kendaraan.no_kendaraan',
                'transaksi.keberangkatan',
                'transaksi.keberangkatan_time',
                'transaksi.kepulangan',
                'transaksi.kepulangan_time',
                'transaksi.status',
                'transaksi.keterangan',
                'transaksi.tipe',
                'transaksi.sisa as kekurangan',
                'transaksi.biaya as total'
            )
                ->selectRaw('(select 
                SUM(CASE WHEN pembayaran.tipe = "dp" THEN pembayaran.nominal ELSE 0 END) as dp
                from pembayaran WHERE pembayaran.id_transaksi = transaksi.id) as dp')
                ->selectRaw('(select 
                SUM(CASE WHEN pembayaran.tipe = "pelunasan" AND pembayaran.metode = "cash" THEN pembayaran.nominal ELSE 0 END) as cash
                from pembayaran WHERE pembayaran.id_transaksi = transaksi.id) as cash')
                ->selectRaw('(SELECT SUM(CASE WHEN pembayaran.tipe = "pelunasan" AND pembayaran.metode = "transfer" THEN pembayaran.nominal ELSE 0 END) as cash
                from pembayaran WHERE pembayaran.id_transaksi = transaksi.id) as transfer')
                ->leftJoin('kendaraan', 'kendaraan.id', '=', 'transaksi.id_kendaraan')
                ->leftJoin('penyewa', 'penyewa.id', '=', 'transaksi.id_penyewa')
                ->whereDate('transaksi.updated_at', $tgl)
                ->orWhere('transaksi.status', '=', 'proses')
                ->get();

            $data2 = Pembayaran::select('pembayaran.*', 'penyewa.nama')
                ->join('transaksi', 'transaksi.id', '=', 'pembayaran.id_transaksi')
                ->join('penyewa', 'penyewa.id', '=', 'transaksi.id_penyewa')
                ->whereDate('pembayaran.created_at', $tgl)
                ->get();

            return response(['data1' => $data, 'data2' => $data2]);
        }

        return view('backend.laporan.harian', compact('config'));
    }

    public function omset_index(Request $request)
    {
        $config['title'] = "Laporan Omset";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Laporan Omset"],
        ];

        $tAwal = $request['tAwal'];
        $tAhir = $request['tAhir'];
        if ($request->ajax()) {
            $data = Transaksi::select('transaksi.id', 'penyewa.nama', 'kendaraan.no_kendaraan', 'transaksi.keberangkatan', 'transaksi.kepulangan', 'transaksi.biaya', 'transaksi.kepulangan_time', 'transaksi.keberangkatan_time')
                ->leftJoin('penyewa', 'transaksi.id_penyewa', '=', 'penyewa.id')
                ->leftJoin('kendaraan', 'transaksi.id_kendaraan', '=', 'kendaraan.id')
                ->whereBetween('kepulangan', [$tAwal, $tAhir])
                ->where('transaksi.status', '=', 'selesai');
            return DataTables::of($data)
                ->addIndexColumn()
                ->make();
        }
        return view('backend.laporan.omset', compact('config'));
    }
}
