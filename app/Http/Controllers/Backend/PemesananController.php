<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\RangeTransaksi;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Traits\ResponseStatus;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Days;
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
            $data = Transaksi::with('penyewa', 'kendaraan')->where(['tipe' => 'pemesanan', 'status' => 'pending'])->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a class="btn btn-xs btn-success" href="' . route('pemesanan.edit', $row->id) . '">Edit</a>
                        <a class="btn btn-xs btn-danger btn-delete" href="#" data-id="' . $row->id . '" >Hapus</a>
                        <a class="btn btn-xs btn-info" href="' . route('pemesanan.show', $row->id) . '">Proses</a>';
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
        $kendaraan = Kendaraan::where('id', $id_kendaraan)->first();
        $config['title'] = "Tambah Pemesanan";
        $config['breadcrumbs'] = [
            ['url' => route('pemesanan.index'), 'title' => "Pemesanan"],
            ['url' => '#', 'title' => "Tambah Pemesanan"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('pemesanan.store')
        ];
        return view('backend.pemesanan.form', compact('config', 'kendaraan'));
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
            'kepulangan' => 'required',
            'dp' => 'required',
            'metode_dp' => 'required',
            'bukti_dp' => $request['metode_dp'] == 'transfer' ? 'required|mimes:jpg,png,jpeg,gif,svg|max:2048' : '',
        ]);
        if ($validator->passes()) {

            $period = new DatePeriod(
                new DateTime($request['keberangkatan']),
                new DateInterval('P1D'),
                new DateTime($request['kepulangan'] . '+1 day')
            );

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
                    'kepulangan' => $request['kepulangan'],
                    'dp' => $request['dp'],
                    'metode_dp' => $request['metode_dp'],
                    'bukti_dp' => $imgTrf,
                    'tipe' => 'pemesanan',
                    'status' => 'pending',
                ]);

                foreach ($period as $key => $value) {
                    RangeTransaksi::create([
                        'id_transaksi' => $data->id,
                        'id_kendaraan' => $request['id_kendaraan'],
                        'tanggal' => $value->format('Y-m-d'),
                    ]);
                }

                DB::commit();
                $response = response()->json($this->responseStore(true, NULL, route('pemesanan.index')));
            } catch (\Throwable $throw) {
                DB::rollBack();
                Log::error($throw);
                $response = response()->json(['error' => $throw->getMessage()]);
            }

            // }

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
        $config['title'] = "Proses Pemesanan";
        $config['breadcrumbs'] = [
            ['url' => route('pemesanan.index'), 'title' => "Pemesanan"],
            ['url' => '#', 'title' => "Proses Pemesan"],
        ];
        $data = Transaksi::where('id', $id)->first();
        // @dd($data);
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('pemesanan.update', $id)
        ];
        return view('backend.pemesanan.proses', compact('config', 'data'));
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
    $validator = Validator::make($request->all(), [
        'status' => 'required|in:proses,batal',
    ]);

    if ($validator->passes()) {
        DB::beginTransaction();
        try {
            $pemesanan = Transaksi::findOrFail($id);

            $pemesanan->status = $request->input('status');
            $pemesanan->kota_tujuan = $request->input('kota_tujuan');
            $pemesanan->kepulangan = $request->input('kepulangan');
            $pemesanan->lama_sewa = $request->input('lama_sewa');
            $pemesanan->paket = $request->input('paket');

            if ($request->input('status') == 'proses') {
                $pemesanan->tipe = 'sewa';
            }

          
            if ($request->input('status') == 'batal') {
                $pemesanan->status = 'batal';
            }

            $pemesanan->save();

            DB::commit();
            $response = response()->json($this->responseStore(true, 'Data berhasil diperbarui', route('pemesanan.index')));
        } catch (\Throwable $throw) {
            DB::rollBack();
            Log::error($throw);
            $response = response()->json(['error' => $throw->getMessage()]);
        }
    } else {
        $response = response()->json(['error' => $validator->errors()->all()]);
    }

    return $response;
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
