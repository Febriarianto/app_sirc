<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Invoice;
use App\Traits\ResponseStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class PenyewaanController extends Controller
{
    use ResponseStatus;

    function __construct()
    {
        $this->middleware('can:penyewaan-list', ['only' => ['index', 'show']]);
        $this->middleware('can:penyewaan-create', ['only' => ['create', 'store']]);
        $this->middleware('can:penyewaan-edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:penyewaan-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $config['title'] = "Penyewaan";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Penyewaan"],
        ];
        if ($request->ajax()) {
            $data = Transaksi::with('penyewa', 'kendaraan')->where(['tipe' => 'sewa', 'status' => 'proses']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a class="btn btn-success" href="' . route('pemesanan.edit', $row->id) . '">Edit</a>
                        <a class="btn btn-info" href="' . route('penyewaan.show', $row->id) . '">Invoice</a>';
                    return $actionBtn;
                })->make();
        }
        return view('backend.penyewaan.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        // $config['title'] = "Tambah Cetak";
        // $config['breadcrumbs'] = [
        //     ['url' => route('pemesanan.index'), 'title' => "Pemesanan"],
        //     ['url' => '#', 'title' => "Tambah Pemesanan"],
        // ];
        // $config['form'] = (object)[
        //     'method' => 'POST',
        //     'action' => route('pemesanan.store')
        // ];
        // return view('backend.invoice.create', compact('config'));
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
        $config['title'] = "Cetak Invoice";
        $config['breadcrumbs'] = [
            ['url' => route('invoice.index'), 'title' => "Cetak"],
            ['url' => '#', 'title' => "Proses Pemesan"],
        ];
        $data = Transaksi::with('penyewa', 'kendaraan')->where('id', $id)->first();
     
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('invoice.update', $id)
        ];
        return view('backend.invoice.create', compact('config', 'data'));
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
