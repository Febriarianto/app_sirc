<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\ResponseStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use PDF;
use Picqer\Barcode\BarcodeGeneratorPNG;


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
            $data = Transaksi::with('penyewa', 'kendaraan')->where(['tipe' => 'sewa', 'status' => 'selesai'])->get();;

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if ($row->keterangan == 'lunas') {
                        $actionBtn = '<a class="btn btn-primary btn-cetak" target="_blank" href="' . route('invoice.cetak', $row->id) . '">Cetak</a>';
                    } else {
                        $actionBtn = '
                        <a class="btn btn-warning btn-cetak" href="' . route('invoice.show', $row->id) . '">Lihat</a>
                        <a class="btn btn-primary btn-cetak" target="_blank" href="' . route('invoice.cetak', $row->id) . '">Cetak</a>';
                    }
                    return $actionBtn;
                })
                ->make();
        }

        return view('backend.invoice.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $config['title'] = "Tambah Pemesanan";
        $config['breadcrumbs'] = [
            ['url' => route('pemesanan.index'), 'title' => "Pemesanan"],
            ['url' => '#', 'title' => "Tambah Pemesanan"],
        ];
        return view('backend.invoice.create', compact('config'));
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

            $file_name = time() . '_' . $request->bukti_pelunasan->getClientOriginalName();
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
        // $invoice = Transaksi::with('penyewa', 'kendaraan', 'invoice')->find($id);
        $invoice = Transaksi::with(['penyewa', 'kendaraan'])->find($id);

        // $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        // $charactersLength = strlen($characters);
        // $randomString = '';
        // for ($i = 0; $i < 5; $i++) {
        //     $randomString .= $characters[random_int(0, $charactersLength - 1)];
        // }

        // $generator = new BarcodeGeneratorPNG();
        // $barcode = base64_encode($generator->getBarcode($randomString.'-'.$invoice->transaksi->id, $generator::TYPE_CODE_128));

        // $pdf = PDF::loadview('backend.invoice.cetak', ['invoice' => $invoice, 'barcode' => $barcode]);
        // // return $pdf->download('invoice-pdf');
        // $ukuran = array(0, 0, 842, 750);
        // $pdf->setPaper($ukuran);
        // return $pdf->stream();
        return view('backend.invoice.cetak', compact('invoice'));
    }

    public function sewaCetak($id)
    {
        $transaksi = Transaksi::with(['penyewa', 'kendaraan'])->find($id);

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 5; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        $generator = new BarcodeGeneratorPNG();
        $barcode = base64_encode($generator->getBarcode($randomString . '-' . $transaksi->id, $generator::TYPE_CODE_128));

        $pdf = PDF::loadview('backend.invoice.sewa-cetak', ['transaksi' => $transaksi, 'barcode' => $barcode]);
        // return $pdf->download('invoice-pdf');
        $ukuran = array(0, 0, 842, 750);
        $pdf->setPaper($ukuran);
        return $pdf->stream();
        // return view('backend.invoice.cetak', compact('invoice', 'data'));
    }


    public function show($id)
    {
        $config['title'] = "Lihat Invoice";
        $config['breadcrumbs'] = [
            ['url' => route('invoice.index'), 'title' => "Cetak"],
            ['url' => '#', 'title' => "Lihat Invoice"],
        ];
        $data = Transaksi::with('penyewa', 'kendaraan')->where('id', $id)->first();
        $pembayaran = Pembayaran::where('id_transaksi', $id)->get();
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('penyewaan.proses', $id)
        ];
        return view('backend.penyewaan.proses', compact('config', 'data', 'pembayaran'));
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
