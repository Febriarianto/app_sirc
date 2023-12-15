<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\RangeTransaksi;
use App\Models\Kendaraan;
use Illuminate\Support\Facades\Storage;
use DateInterval;
use DatePeriod;
use DateTime;
use Carbon\Carbon;
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
            $data = Transaksi::with('penyewa', 'kendaraan')->where(['tipe' => 'sewa', 'status' => 'proses'])->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a class="btn btn-success" href="' . route('penyewaan.edit', $row->id) . '">Edit</a>';
                    $actionBtn = '<a class="btn btn-success" href="' . route('penyewaan.edit_sewa', [$row->id, $row->id_kendaraan]) . '">Edit</a>';


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
    public function create($id_kendaraan, $tanggal)
    {
        $kendaraan = Kendaraan::where('id', $id_kendaraan)->first();
        $config['title'] = "Tambah Penyewaan";
        $config['breadcrumbs'] = [
            ['url' => route('penyewaan.index'), 'title' => "Penyewaan"],
            ['url' => '#', 'title' => "Tambah Penyewaan"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('penyewaan.store')
        ];

        $dataTransaksi = Kendaraan::where('id', $id_kendaraan)
            ->with(['jenis'])
            ->first();

        $tanggal = $tanggal;
        return view('backend.penyewaan.form', compact('config', 'kendaraan', 'dataTransaksi', 'tanggal'));
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
            'harga_sewa' => 'required',
            'kota_tujuan' => 'required',
            'bukti_dp' => $request['metode_dp'] == 'transfer' ? 'nullable|mimes:jpg,png,jpeg,gif,svg|max:2048' : '',
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
                    $fileTrf = $request->file('bukti_dp');
                    $imgTrf = $fileTrf->getClientOriginalName();
                    $fileTrf->storeAs('public/buktiDP/', $imgTrf);
                } else {
                    $imgTrf = '';
                }

                if ($request->hasFile('bukti_pelunasan')) {
                    $fileP = $request->file('bukti_pelunasan');
                    $imgP = $fileP->getClientOriginalName();
                    $fileP->storeAs('public/buktiPelunasan/', $imgP);
                } else {
                    $imgP = '';
                }

                $keberangkatan_time = Carbon::now();
                $data = Transaksi::create([
                    'id_penyewa' => $request['id_penyewa'],
                    'id_kendaraan' => $request['id_kendaraan'],
                    'keberangkatan' => $request['keberangkatan'],
                    'keberangkatan_time' => $keberangkatan_time,
                    'kepulangan' => $request['kepulangan'],
                    'dp' => $request['dp'],
                    'metode_dp' => $request['metode_dp'],
                    'bukti_dp' => $imgTrf,
                    'metode_pelunasan' => $request['metode_pelunasan'],
                    'bukti_pelunasan' => $imgP,
                    'tipe' => 'sewa',
                    'status' => 'proses',
                    'lama_sewa' => $request['lama_sewa'],
                    'harga_sewa' => $request['harga_sewa'],
                    'biaya' => $request['biaya'],
                    'sisa' => $request['sisa'],
                    'kota_tujuan' => $request['kota_tujuan'],
                ]);

                foreach ($period as $key => $value) {
                    RangeTransaksi::create([
                        'id_transaksi' => $data->id,
                        'id_kendaraan' => $request['id_kendaraan'],
                        'tanggal' => $value->format('Y-m-d'),
                    ]);
                }

                DB::commit();
                $response = response()->json($this->responseStore(true, NULL, route('penyewaan.index')));
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
        $config['title'] = "Cetak Invoice";
        $config['breadcrumbs'] = [
            ['url' => route('invoice.index'), 'title' => "Cetak"],
            ['url' => '#', 'title' => "Proses Pemyewaan"],
        ];
        $data = Transaksi::with('penyewa', 'kendaraan')->where('id', $id)->first();

        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('penyewaan.proses', $id)
        ];
        return view('backend.penyewaan.proses', compact('config', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $id_kendaraan)
    {
        $config['title'] = "Edit Penyewaan";
        $config['breadcrumbs'] = [
            ['url' => route('penyewaan.index'), 'title' => "Penyewaan"],
            ['url' => '#', 'title' => "Edit Pemesan"],
        ];

        $data = Transaksi::where('id', $id)->first();
        $kendaraan = Kendaraan::where('id', $id_kendaraan)->first();

        $dataTransaksi = Transaksi::where('id_kendaraan', $id_kendaraan)
            ->with(['kendaraan.jenis:id,nama,harga_12,harga_24'])
            ->first();

        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('penyewaan.update', $id)
        ];

        return view('backend.penyewaan.form', compact('config', 'data', 'kendaraan', 'dataTransaksi'));
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
            'kepulangan' => 'required',
            // 'over_time' => 'required',
            'biaya' => 'required',
            'sisa' => 'required',
            // 'metode_pelunasan' => 'required',
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

                if ($request['metode_pelunasan'] == 'transfer' && isset($request['bukti_pelunasan'])) {
                    $file = $request->file('bukti_pelunasan');
                    $filename = $file->getClientOriginalName();
                    $file->storeAs('public/buktiPelunasan/', $filename);
                    Storage::delete('public/buktiPelunasan/' . $data->bukti_pelunasan);
                } else {
                    $filename = $data->bukti_pelunasan;
                }

                $data->update([
                    'dp' => $request['dp'],
                    'kepulangan' => $request['kepulangan'],
                    'metode_pelunasan' => $request['metode_pelunasan'],
                    'lama_sewa' => $request['lama_sewa'],
                    'over_time' => $request['over_time'],
                    'biaya' => $request['biaya'],
                    'sisa' => $request['sisa'],
                    'bukti_pelunasan' => $filename,
                    'bukti_dp' => $imgTrf,
                    'keterangan' => $request['keterangan'],
                ]);

                foreach ($period as $key => $value) {
                    RangeTransaksi::create([
                        'id_transaksi' => $data->id,
                        'id_kendaraan' => $request['id_kendaraan'],
                        'tanggal' => $value->format('Y-m-d'),
                    ]);
                }

                DB::commit();
                $response = response()->json($this->responseStore(true, NULL, route('penyewaan.index')));
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

    public function proses(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kepulangan' => 'required',
            'over_time' => 'required',
            'biaya' => 'required',
            'sisa' => 'required',
            'metode_pelunasan' => 'required',
        ]);
        if ($validator->passes()) {

            DB::beginTransaction();
            try {

                $kepulangan_time = Carbon::now();
                $data = Transaksi::find($id);

                if ($request['metode_pelunasan'] == 'transfer' && isset($request['bukti_pelunasan'])) {
                    $file = $request->file('bukti_pelunasan');
                    $filename = $file->getClientOriginalName();
                    $file->storeAs('public/buktiPelunasan/', $filename);
                } else {
                    $filename = $data->bukti_pelunasan;
                }

                $data->update([
                    'kepulangan' => $request['kepulangan'],
                    'kepulangan_time' => $kepulangan_time,
                    'metode_pelunasan' => $request['metode_pelunasan'],
                    'over_time' => $request['over_time'],
                    'biaya' => $request['biaya'],
                    'sisa' => $request['sisa'],
                    'bukti_pelunasan' => $filename,
                    'keterangan' => $request['keterangan'] ?? '',
                    'status' => 'selesai',
                ]);

                $delRange = RangeTransaksi::where('id_transaksi', $data->id)->delete();

                DB::commit();
                $response = response()->json($this->responseStore(true, NULL, route('penyewaan.index')));
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
        //
    }
}
