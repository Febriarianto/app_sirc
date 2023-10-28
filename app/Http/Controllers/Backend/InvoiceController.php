<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\ResponseStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use PDF;


class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    
    {
        $config['title'] = "Invoice";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Invoice"],
        ];

        if ($request->ajax()) {
            $data = Invoice::with(['penyewa','kendaraan']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a class="btn btn-success" href="' . route('invoice.edit', $row->id) . '">Edit</a>
                                  <a class="btn btn-primary btn-cetak" href="' . route('invoice.cetak', $row->id) . '">Cetak</a>';
                    return $actionBtn;
                })->make();
        }
        return view('backend.invoice.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id_kendaraan)
    {
        $invoice = Invoice::where('id', $id_kendaraan)->get();
        $config['title'] = "Tambah Pemesanan";
        $config['breadcrumbs'] = [
            ['url' => route('pemesanan.index'), 'title' => "Pemesanan"],
            ['url' => '#', 'title' => "Tambah Pemesanan"],
        ];
        return view('backend.invoice.create', compact('config', 'invoice'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InvoiceRequest $request)
    {
        $data = $request->all();

        // if ($request['metode_pelunasan'] == 'transfer') {
        if ($request->hasFile('bukti_pelunasan')) {
            
            $file_name = time().'_'.$request->bukti_pelunasan->getClientOriginalName();
            $bukti_pelunasan = $request->bukti_pelunasan->storeAs('bukti_pelunasan', $file_name);

            $data['bukti_pelunasan'] = $bukti_pelunasan;
        } else {
            $bukti_pelunasan = '';
        }

        Invoice::create($data);

        return redirect()->route('invoice.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cetak($id)
    {
        $invoice = Invoice::find($id);
    	$pdf = PDF::loadview('backend.invoice.cetak',['invoice'=>$invoice]);
    	// return $pdf->download('invoice-pdf');
        $ukuran = array(0,0,842,750);
        $pdf->setPaper($ukuran);
        return $pdf->stream();
        return view('backend.invoice.cetak', compact('invoice', 'data'));
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
        $response = response()->json([
            'status' => 'error',
            'message' => 'Data gagal dihapus'
        ]);
        $data = Invoice::find($id);
        DB::beginTransaction();
        try {
            $data->delete();
            // \Storage::delete($data->ktp);
            DB::commit();
            $response = response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Throwable $throw) {
            Log::error($throw);
            $response = response()->json(['error' => $throw->getMessage()]);
        }
        return $response;
    }
}
