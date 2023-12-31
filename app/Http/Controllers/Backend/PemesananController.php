<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\RangeTransaksi;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Traits\ResponseStatus;
use Illuminate\Support\Facades\Storage;
use DateInterval;
use DatePeriod;
use DateTime;
use Carbon\Carbon;
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
    public function create($id_kendaraan, $tanggal)
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
        $tanggal = $tanggal;
        return view('backend.pemesanan.form', compact('config', 'kendaraan', 'tanggal'));
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
            $cehTgl = RangeTransaksi::where([['id_kendaraan', $request->id_kendaraan], ['tanggal', $request->keberangkatan]])->first();
            if ($cehTgl == null) {
                $period = new DatePeriod(
                    new DateTime($request['keberangkatan']),
                    new DateInterval('P1D'),
                    new DateTime($request['kepulangan'] . '+1 day')
                );

                DB::beginTransaction();
                try {
                    if ($request['metode_dp'] == 'transfer') {
                        $fileTrf = $request->file('bukti_dp');
                        $imgTrf = $fileTrf->getClientOriginalName();
                        $fileTrf->storeAs('public/buktiDP/', $imgTrf);
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
            } else {
                $response = response()->json([
                    'status' => 'Gagal',
                    'message' => 'Mobil di Tanggal Tersebut sudah di booking'
                ]);
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
        $config['title'] = "Proses Pemesanan";
        $config['breadcrumbs'] = [
            ['url' => route('pemesanan.index'), 'title' => "Pemesanan"],
            ['url' => '#', 'title' => "Proses Pemesan"],
        ];
        $data = Transaksi::where('id', $id)->first();
        // @dd($data);
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('pemesanan.proses', $id)
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
        return view('backend.pemesanan.form', compact('config', 'data'));
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
            'id_penyewa' => 'required',
            'id_kendaraan' => 'required',
            'keberangkatan' => 'required',
            'kepulangan' => 'required',
            'dp' => 'required',
            'metode_dp' => 'required',
            'bukti_dp' => isset($request['bukti_dp']) ? 'required|mimes:jpg,png,jpeg,gif,svg|max:2048' : '',
        ]);
        if ($validator->passes()) {

            $period = new DatePeriod(
                new DateTime($request['keberangkatan']),
                new DateInterval('P1D'),
                new DateTime($request['kepulangan'] . '+1 day')
            );

            $dataTgl = RangeTransaksi::where('id_transaksi', $id)->delete();

            DB::beginTransaction();
            try {
                $data = Transaksi::find($id);

                if ($request['metode_dp'] == 'transfer' && isset($request['bukti_dp'])) {
                    $fileTrf = $request->file('bukti_dp');
                    $imgTrf = $fileTrf->getClientOriginalName();
                    $fileTrf->storeAs('public/buktiDP/', $imgTrf);
                    Storage::delete('public/buktiDP/' . $data->bukti_dp);
                } else {
                    $imgTrf = $data->bukti_dp;
                }

                $data->update([
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
        } else {
            $response = response()->json(['error' => $validator->errors()->all()]);
        }
        return $response;
    }

    public function proses(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:proses,batal',
        ]);

        if ($validator->passes()) {
            DB::beginTransaction();
            try {
                $pemesanan = Transaksi::findOrFail($id);
                $keberangkatan_time = Carbon::now();
                if ($request->input('status') == 'proses' &&  $request->input('keberangkatan') == Carbon::now()->format('Y-m-d')) {
                    $pemesanan->status = $request->input('status');
                    $pemesanan->keberangkatan_time = $keberangkatan_time;
                    $pemesanan->kota_tujuan = $request->input('kota_tujuan');
                    $pemesanan->kepulangan = $request->input('kepulangan');
                    $pemesanan->lama_sewa = $request->input('lama_sewa');
                    $pemesanan->paket = $request->input('paket');
                    $pemesanan->harga_sewa = $request->input('harga_sewa');
                    $pemesanan->tipe = 'sewa';
                    $pemesanan->save();
                    DB::commit();
                    $response = response()->json($this->responseStore(true, 'Data berhasil diperbarui', route('pemesanan.index')));
                } else {
                    $response = response()->json([
                        'status' => 'Gagal!',
                        'message' => 'Tidak Bisa di proses sebelum tanggal Keberangaktan'
                    ]);
                }
                if ($request->input('status') == 'batal') {
                    $pemesanan->status = 'batal';
                    $pemesanan->save();
                    DB::commit();
                    $response = response()->json($this->responseStore(true, 'Data berhasil diperbarui', route('pemesanan.index')));
                }
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
        $dataTgl = RangeTransaksi::where('id_transaksi', $id)->delete();
        DB::beginTransaction();
        try {
            $data->delete();
            Storage::delete('public/buktiDP/' . $data->bukti_dp);
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
