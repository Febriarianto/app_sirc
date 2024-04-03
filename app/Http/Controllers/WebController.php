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
        if ($request['jenis'] != NULL) {
            $kendaraan = Kendaraan::select(
                'kendaraan.id',
                'kendaraan.no_kendaraan',
                'kendaraan.tahun',
                'kendaraan.warna',
                'jenis.nama',
                'kendaraan.status',
                'jenis.harga_12',
                'jenis.harga_24'
            )
                ->selectRaw('(select count(id) from transaksi where id_kendaraan = kendaraan.id and transaksi.status = "proses") as s ')
                ->leftJoin('jenis', 'jenis.id', '=', 'kendaraan.id_jenis')
                ->where('kendaraan.status', '=', 'aktif')
                ->where('jenis.id', $request['jenis'])
                ->paginate(6);
            $id_jenis = $request['jenis'];
        } else {
            $kendaraan =  Kendaraan::select(
                'kendaraan.id',
                'kendaraan.no_kendaraan',
                'kendaraan.tahun',
                'kendaraan.warna',
                'jenis.nama',
                'kendaraan.status',
                'jenis.harga_12',
                'jenis.harga_24'
            )
                ->selectRaw('(select count(id) from transaksi where id_kendaraan = kendaraan.id and transaksi.status = "proses") as s ')
                ->leftJoin('jenis', 'jenis.id', '=', 'kendaraan.id_jenis')
                ->where('kendaraan.status', '=', 'aktif')
                ->paginate(6);
            $id_jenis = '';
        }
        $jenis = Jenis::select('id', 'nama')->get();
        return view('web.index', compact('kendaraan', 'jenis', 'id_jenis'));
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
