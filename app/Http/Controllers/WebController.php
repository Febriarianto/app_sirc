<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kendaraan;
use App\Models\Jenis;
use App\Models\range_transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use League\CommonMark\Node\Query\AndExpr;

class WebController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request['jenis'] != NULL && $request['tgl'] != NULL) {
            $kendaraan = DB::table('kendaraan')
                ->select('kendaraan.id', 'kendaraan.no_kendaraan', 'kendaraan.tahun', 'kendaraan.warna', 'kendaraan.foto', 'jenis.nama', 'jenis.harga_12', 'jenis.harga_24', 'range_transaksi.tanggal', 'range_transaksi.id_kendaraan')
                ->leftJoin('jenis', 'jenis.id', '=', 'kendaraan.id_jenis')
                ->leftJoin('range_transaksi', function ($join) use ($request) {
                    $join->on('kendaraan.id', '=', 'range_transaksi.id_kendaraan')->where('range_transaksi.tanggal', $request['tgl']);
                })
                ->where('jenis.id', $request['jenis'])
                ->where('kendaraan.status', '=', 'aktif')
                ->paginate(6);
            $id_jenis = $request['jenis'];
            $tgl = $request['tgl'];
        } else if ($request['jenis'] != NULL) {
            $kendaraan = DB::table('kendaraan')
                ->select('kendaraan.id', 'kendaraan.no_kendaraan', 'kendaraan.tahun', 'kendaraan.warna', 'kendaraan.foto', 'jenis.nama', 'jenis.harga_12', 'jenis.harga_24', 'range_transaksi.tanggal', 'range_transaksi.id_kendaraan')
                ->leftJoin('jenis', 'jenis.id', '=', 'kendaraan.id_jenis')
                ->leftJoin('range_transaksi', function ($join) {
                    $join->on('kendaraan.id', '=', 'range_transaksi.id_kendaraan')->where('range_transaksi.tanggal', Carbon::now()->format('Y-m-d'));
                })
                ->where('jenis.id', $request['jenis'])
                ->where('kendaraan.status', '=', 'aktif')
                ->paginate(6);
            $id_jenis = $request['jenis'];
            $tgl = '';
        } else if ($request['tgl'] != NULL) {
            $kendaraan = DB::table('kendaraan')
                ->select('kendaraan.id', 'kendaraan.no_kendaraan', 'kendaraan.tahun', 'kendaraan.warna', 'kendaraan.foto', 'jenis.nama', 'jenis.harga_12', 'jenis.harga_24', 'range_transaksi.tanggal', 'range_transaksi.id_kendaraan')
                ->leftJoin('jenis', 'jenis.id', '=', 'kendaraan.id_jenis')
                ->leftJoin('range_transaksi', function ($join) use ($request) {
                    $join->on('kendaraan.id', '=', 'range_transaksi.id_kendaraan')->where('range_transaksi.tanggal', $request['tgl']);
                })
                ->where('kendaraan.status', '=', 'aktif')
                ->paginate(6);
            $id_jenis = '';
            $tgl = $request['tgl'];
        } else {
            $kendaraan = DB::table('kendaraan')
                ->select('kendaraan.id', 'kendaraan.no_kendaraan', 'kendaraan.tahun', 'kendaraan.warna', 'kendaraan.foto', 'jenis.nama', 'jenis.harga_12', 'jenis.harga_24', 'range_transaksi.tanggal', 'range_transaksi.id_kendaraan')
                ->leftJoin('jenis', 'jenis.id', '=', 'kendaraan.id_jenis')
                ->leftJoin('range_transaksi', function ($join) {
                    $join->on('kendaraan.id', '=', 'range_transaksi.id_kendaraan')->where('range_transaksi.tanggal', Carbon::now()->format('Y-m-d'));
                })
                ->where('kendaraan.status', '=', 'aktif')
                ->paginate(6);
            $id_jenis = '';
            $tgl = '';
        }
        $jenis = Jenis::select('id', 'nama')->get();
        return view('web.index', compact('kendaraan', 'jenis', 'tgl', 'id_jenis'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
