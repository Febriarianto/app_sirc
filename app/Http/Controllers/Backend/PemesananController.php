<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Traits\ResponseStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class PemesananController extends Controller
{
    use ResponseStatus;

    function __construct()
    {
        $this->middleware('can:pemesanan-list', ['only' => ['index', 'show']]);
        $this->middleware('can:pemesanan-create', ['only' => ['create', 'store']]);
        $this->middleware('can:pemesanan-edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:pemesanan-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $config['title'] = "Pemesanan";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Pemesanan"],
        ];
        if ($request->ajax()) {
            $data = Transaksi::with('penyewa', 'kendaraan')->where('tipe', '=', 'pemesanan')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a class="btn btn-success" href="' . route('pemesanan.edit', $row->id) . '">Edit</a>
                        <a class="btn btn-danger btn-delete" href="#" data-id="' . $row->id . '" >Hapus</a>
                        <a class="btn btn-info" href="' . route('pemesanan.edit', $row->id) . '">Proses</a>';
                    return $actionBtn;
                })->make();
        }
        return view('backend.pemesanan.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id_kendaraan)
    {
        $kendaraan = Kendaraan::where('id', $id_kendaraan)->get();
        $config['title'] = "Tambah Pemesanan";
        $config['breadcrumbs'] = [
            ['url' => route('pemesanan.index'), 'title' => "Pemesanan"],
            ['url' => '#', 'title' => "Tambah Pemesanan"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('pemesanan.store')
        ];
        return view('backend.pemesanan.form', compact('config', 'id_kendaraan','kendaraan'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_penyewa' => 'required',
            'id_kendaraan' => 'required',
            'keberangkatan' => 'required',
            'dp' => 'required',
            'metode_dp' => 'required',
            'bukti_dp' => $request['metode_dp'] == 'transfer' ? 'required|mimes:jpg,png,jpeg,gif,svg|max:2048' : '',
        ]);
        if ($validator->passes()) {
            $cekPemesanan = Transaksi::select('id')
                ->where('id_kendaraan', $request['id_kendaraan'])
                ->where('keberangkatan', $request['keberangkatan'])
                ->first();
            if ($cekPemesanan == !null) {
                $response = response()->json($this->responseStore(false, 'Mobil Sudah di Booking , Pada Tanggal Tersebut', NULL));
            } else {
                DB::beginTransaction();
                try {
                    if ($request['metode_dp'] == 'transfer') {
                        $imgTrf = $request->file('bukti_dp')->store('buktiDP', 'public');
                    } else {
                        $imgTrf = '';
                    }
                    $data = Transaksi::create([
                        'id_penyewa' => $request['id_penyewa'],
                        'id_kendaraan' => $request['id_kendaraan'],
                        'keberangkatan' => $request['keberangkatan'],
                        'dp' => $request['dp'],
                        'metode_dp' => $request['metode_dp'],
                        'bukti_dp' => $imgTrf,
                        'tipe' => 'pemesanan',
                    ]);

                    DB::commit();
                    $response = response()->json($this->responseStore(true, NULL, route('pemesanan.index')));
                } catch (\Throwable $throw) {
                    DB::rollBack();
                    Log::error($throw);
                    $response = response()->json(['error' => $throw->getMessage()]);
                }
            }
        } else {
            $response = response()->json(['error' => $validator->errors()->all()]);
        }
        return $response;
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
        $config['title'] = "Edit Pemesanan";
        $config['breadcrumbs'] = [
            ['url' => route('pemesanan.index'), 'title' => "Pemesanan"],
            ['url' => '#', 'title' => "Edit Pemesan"],
        ];
        $data = Transaksi::with('penyewa', 'kendaraan')->where('id', $id)->first();
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('pemesanan.update', $id)
        ];
        return view('backend.pemesanan.edit', compact('config', 'data'));
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
        $data = Transaksi::find($id);
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
