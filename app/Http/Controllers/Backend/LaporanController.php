<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Pembayaran;
use App\Models\Kendaraan;
use App\Models\HargaKendaraan;
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
        $config['title'] = "Laporan Kendaraan";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Laporan Kendaraan"],
        ];

        $tAwal = $request['tAwal'] ?? Carbon::now()->format('Y-m-d');
        $tAhir = $request['tAhir'] ?? Carbon::now()->format('Y-m-d');
        if ($request->ajax()) {
            $data = Transaksi::where('id_kendaraan', $request['kendaraan'])
                ->select('transaksi.id', 'penyewa.nama', 'transaksi.keberangkatan', 'transaksi.kepulangan', 'transaksi.biaya', 'transaksi.kepulangan_time', 'transaksi.keberangkatan_time', 'transaksi.keterangan')
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
        $tAwal = $request['tAwal'];
        $tAhir = $request['tAhir'];
        if ($request->ajax()) {
            $data = Transaksi::select('transaksi.id', 'penyewa.nama', 'transaksi.keberangkatan', 'transaksi.kepulangan', 'transaksi.biaya')
                ->leftJoin('penyewa', 'transaksi.id_penyewa', '=', 'penyewa.id')
                ->selectRaw('biaya * 0.1 as komisi')
                ->where('penyewa.referral_id', '=', $request['referral'])
                ->whereBetween('kepulangan', [$tAwal, $tAhir])
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
        $tgl = $request['tgl'];
        $param = $request['param'];
        if ($request->ajax()) {
            if ($param == 'dt') {
                $data = Transaksi::select(
                    'transaksi.id',
                    'transaksi.id_kendaraan',
                    'transaksi.id_penyewa',
                    'penyewa.nama',
                    'kendaraan.no_kendaraan',
                    'transaksi.keberangkatan',
                    'transaksi.keberangkatan_time',
                    'transaksi.status',
                    'transaksi.keterangan',
                    'transaksi.tipe',
                    'transaksi.sisa as kekurangan',
                    'transaksi.biaya as total'
                )
                    ->selectRaw('(SELECT SUM(CASE WHEN pembayaran.tipe = "dp" THEN pembayaran.nominal ELSE 0 END) as dp from pembayaran WHERE pembayaran.id_transaksi = transaksi.id) as dp')
                    ->selectRaw('(SELECT SUM(CASE WHEN pembayaran.tipe = "titip" THEN pembayaran.nominal ELSE 0 END) as titip from pembayaran WHERE pembayaran.id_transaksi = transaksi.id) as titip')
                    ->selectRaw('(SELECT SUM(CASE WHEN pembayaran.tipe = "pelunasan" THEN pembayaran.nominal ELSE 0 END) as pelunasan from pembayaran WHERE pembayaran.id_transaksi = transaksi.id) as pelunasan')
                    ->leftJoin('kendaraan', 'kendaraan.id', '=', 'transaksi.id_kendaraan')
                    ->leftJoin('penyewa', 'penyewa.id', '=', 'transaksi.id_penyewa')
                    ->where('transaksi.status', '=', 'proses')
                    ->get();
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('dp', function ($row) {
                        if ($row->dp == null) {
                            return 0;
                        } else {
                            return $row->dp;
                        }
                    })
                    ->addColumn('titip', function ($row) {
                        if ($row->titip == null) {
                            return 0;
                        } else {
                            return $row->titip;
                        }
                    })
                    ->addColumn('pelunasan', function ($row) {
                        if ($row->pelunasan == null) {
                            return 0;
                        } else {
                            return $row->pelunasan;
                        }
                    })
                    ->addColumn('durasi', function ($row) {
                        $start = Carbon::parse($row->keberangkatan . $row->keberangkatan_time);
                        $end =  Carbon::parse();
                        $duration = $end->diff($start);
                        return  $end->diffInDays($start) . ' Hari ' . $duration->format('%H') . ' Jam';
                    })
                    ->addColumn('harga_sewa', function ($row) {
                        $start = Carbon::parse($row->keberangkatan . $row->keberangkatan_time);
                        $end =  Carbon::parse();
                        $duration = $end->diff($start);
                        $hari = $end->diffInDays($start);
                        $jam = $duration->format('%H');
                        $toleransi = 1;

                        $get_harga_harian = HargaKendaraan::SELECT('harga_kendaraan.id', 'harga.nilai', 'harga.nominal')
                            ->JOIN('harga', 'harga.id', '=', 'harga_kendaraan.id_harga')
                            ->WHERE('harga_kendaraan.id_kendaraan', $row->id_kendaraan)
                            ->WHERE('harga.nilai', '=', 24)
                            ->first();

                        if ($hari == 0 && $jam == 0 | $hari == 0 && $jam - $toleransi == 0) {
                            $get_harga_sql = HargaKendaraan::SELECT('harga_kendaraan.id', 'harga.nilai', 'harga.nominal')
                                ->JOIN('harga', 'harga.id', '=', 'harga_kendaraan.id_harga')
                                ->WHERE('harga_kendaraan.id_kendaraan', $row->id_kendaraan)
                                ->WHERE('harga.nilai', '>=', $jam)
                                ->ORDERBY('harga.nilai', 'ASC')
                                ->first();
                            $get_harga_jam = $get_harga_sql->nominal;
                        } elseif ($hari !== 0 && $jam == 0 | $hari !== 0 && $jam - $toleransi == 0) {
                            $get_harga_jam = 0;
                        } else {
                            $get_harga_sql = HargaKendaraan::SELECT('harga_kendaraan.id', 'harga.nilai', 'harga.nominal')
                                ->JOIN('harga', 'harga.id', '=', 'harga_kendaraan.id_harga')
                                ->WHERE('harga_kendaraan.id_kendaraan', $row->id_kendaraan)
                                ->WHERE('harga.nilai', '>=', $jam - $toleransi)
                                ->ORDERBY('harga.nilai', 'ASC')
                                ->first();
                            $get_harga_jam = $get_harga_sql->nominal;
                        }

                        $harga = $hari * $get_harga_harian->nominal + $get_harga_jam * 1;
                        return  $harga;
                    })
                    ->make();
            } else {
                $data = Transaksi::select(
                    'transaksi.id',
                    'transaksi.id_kendaraan',
                    'transaksi.id_penyewa',
                    'penyewa.nama',
                    'transaksi.durasi',
                    'kendaraan.no_kendaraan',
                    'transaksi.keberangkatan',
                    'transaksi.keberangkatan_time',
                    'transaksi.kepulangan',
                    'transaksi.kepulangan_time',
                    'transaksi.harga_sewa',
                    'transaksi.status',
                    'transaksi.keterangan',
                    'transaksi.tipe',
                    'transaksi.diskon',
                    'transaksi.sisa as kekurangan',
                    'transaksi.biaya as total'
                )
                    ->selectRaw('(select 
                        SUM(CASE WHEN pembayaran.tipe = "dp" THEN pembayaran.nominal ELSE 0 END) as dp
                        from pembayaran WHERE pembayaran.id_transaksi = transaksi.id) as dp')
                    ->selectRaw('(select 
                        SUM(CASE WHEN pembayaran.tipe = "titip" THEN pembayaran.nominal ELSE 0 END) as titip
                        from pembayaran WHERE pembayaran.id_transaksi = transaksi.id) as titip')
                    ->selectRaw('(SELECT SUM(CASE WHEN pembayaran.tipe = "pelunasan" THEN pembayaran.nominal ELSE 0 END) as pelunasan
                        from pembayaran WHERE pembayaran.id_transaksi = transaksi.id) as pelunasan')
                    ->leftJoin('kendaraan', 'kendaraan.id', '=', 'transaksi.id_kendaraan')
                    ->leftJoin('penyewa', 'penyewa.id', '=', 'transaksi.id_penyewa')
                    ->where('transaksi.kepulangan', $tgl)
                    ->where('transaksi.status', '=', 'selesai')
                    ->get();
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->make();
            }
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
            $data = Transaksi::select('kendaraan.no_kendaraan',)
                ->selectRaw('sum(transaksi.biaya) as biaya')
                // ->select('transaksi.id', 'penyewa.nama', 'kendaraan.no_kendaraan', 'transaksi.keberangkatan', 'transaksi.kepulangan', 'transaksi.biaya', 'transaksi.kepulangan_time', 'transaksi.keberangkatan_time')
                ->leftJoin('penyewa', 'transaksi.id_penyewa', '=', 'penyewa.id')
                ->leftJoin('kendaraan', 'transaksi.id_kendaraan', '=', 'kendaraan.id')
                ->whereBetween('kepulangan', [$tAwal, $tAhir])
                ->where('transaksi.status', '=', 'selesai')
                // ->orderBy('kendaraan.no_kendaraan', 'ASC');
                ->groupBy('kendaraan.no_kendaraan');
            return DataTables::of($data)
                ->addIndexColumn()
                ->make();
        }
        return view('backend.laporan.omset', compact('config'));
    }

    public function detail(Request $request)
    {
        $config['title'] = "Laporan Harian";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Laporan Harian"],
        ];

        if ($request->ajax()) {
            $id = $_GET['id'];
            $data = Pembayaran::select('id', 'id_transaksi', 'tipe', 'metode', 'nominal')
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d") as date')
                ->where('id_transaksi', $id)
                ->get();

            return response(['detail' => $data]);
        }
    }

    public function keuangan(Request $request)
    {
        $config['title'] = "Laporan Keuangan";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Laporan Keuangan"],
        ];
        $tgl = $request['tgl'];
        $param = $request['param'];
        if ($request->ajax()) {
            if ($param == 'dt') {
                $data = Pembayaran::select('*')
                    ->selectRaw("(SELECT penyewa.nama FROM penyewa JOIN transaksi ON transaksi.id_penyewa = penyewa.id WHERE transaksi.id = id_transaksi ) as nama,
                    CASE WHEN status = 'pemasukan' AND metode = 'cash' THEN nominal END AS 'pc', CASE WHEN status = 'pemasukan' AND metode = 'transfer' THEN nominal END AS 'pf' ")
                    ->whereDate('pembayaran.created_at', $tgl)
                    ->where('status', '=', 'pemasukan')
                    ->get();
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('tgl', function ($row) {
                        $tgl = Carbon::parse($row->created_at);
                        return $tgl;
                    })
                    ->make();
            } else {
                $data = Pembayaran::select('*')
                    ->selectRaw("(SELECT penyewa.nama FROM penyewa JOIN transaksi ON transaksi.id_penyewa = penyewa.id WHERE transaksi.id = id_transaksi ) as nama,
                CASE WHEN status = 'pengeluaran' AND metode = 'cash' THEN nominal END AS 'pc', CASE WHEN status = 'pengeluaran' AND metode = 'transfer' THEN nominal END AS 'pf' ")
                    ->whereDate('pembayaran.created_at', $tgl)
                    ->where('status', '=', 'pengeluaran')
                    ->get();
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('tgl', function ($row) {
                        $tgl = Carbon::parse($row->created_at);
                        return $tgl;
                    })
                    ->make();
            }
        }

        return view('backend.laporan.keuangan', compact('config'));
    }

    public function belum_lunas(Request $request)
    {
        $config['title'] = "Laporan Inv. Belum Lunas";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Laporan Inv. Belum Lunas"],
        ];
        $tAwal = $request['tAwal'];
        $tAhir = $request['tAhir'];
        if ($request->ajax()) {
            $data = Transaksi::select(
                'transaksi.id',
                'penyewa.nama',
                'kendaraan.no_kendaraan',
                'transaksi.durasi',
                'transaksi.keberangkatan',
                'transaksi.kepulangan',
                'transaksi.sisa',
                'transaksi.biaya'
            )
                ->leftJoin('penyewa', 'transaksi.id_penyewa', '=', 'penyewa.id')
                ->leftJoin('kendaraan', 'transaksi.id_kendaraan', '=', 'kendaraan.id')
                ->where('transaksi.status', '=', 'selesai')
                ->where('transaksi.keterangan', '=', 'belum lunas')
                ->whereBetween('transaksi.kepulangan', [$tAwal, $tAhir]);
            return DataTables::of($data)
                ->addIndexColumn()
                ->make();
        }

        return view('backend.laporan.belum_lunas', compact('config'));
    }

    public function uang_keluar(Request $request)
    {
        $config['title'] = "Laporan Uang Keluar";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Laporan Uang Keluar"],
        ];
        $tAwal = $request['tAwal'];
        $tAhir = $request['tAhir'];
        if ($request->ajax()) {
            $data = Pembayaran::select('*')
                ->selectRaw("(SELECT penyewa.nama FROM penyewa JOIN transaksi ON transaksi.id_penyewa = penyewa.id WHERE transaksi.id = id_transaksi ) as nama,
                CASE WHEN status = 'pengeluaran' AND metode = 'cash' THEN nominal END AS 'pc', CASE WHEN status = 'pengeluaran' AND metode = 'transfer' THEN nominal END AS 'pf' ")
                ->whereBetween('pembayaran.created_at', [$tAwal, $tAhir])
                ->where('status', '=', 'pengeluaran')
                ->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tgl', function ($row) {
                    $tgl = Carbon::parse($row->created_at)->format('Y-m-d');
                    return $tgl;
                })
                ->make();
        }

        return view('backend.laporan.uang_keluar', compact('config'));
    }
}
