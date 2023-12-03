<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\RangeTransaksi;
use App\Models\Kendaraan;
use DateInterval;
use DatePeriod;
use DateTime;
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
    public function create($id_kendaraan)
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

        $dataTransaksi = Transaksi::where('id_kendaraan', $id_kendaraan)
            ->with(['kendaraan.jenis:id,nama,harga_12,harga_24'])
            ->first();

        return view('backend.penyewaan.form', compact('config', 'kendaraan', 'dataTransaksi'));
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
                    'tipe' => 'sewa',
                    'status' => 'proses',
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
    public function edit($id)
    {
        $config['title'] = "Edit Penyewaan";
        $config['breadcrumbs'] = [
            ['url' => route('penyewaan.index'), 'title' => "Penyewaan"],
            ['url' => '#', 'title' => "Edit Pemesan"],
        ];
        $data = Transaksi::where('id', $id)->first();
        // @dd($data);
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('penyewaan.update', $id)
        ];
        return view('backend.penyewaan.form', compact('config', 'data'));
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
            'over_time' => 'required',
            'biaya' => 'required',
            'sisa' => 'required',
            'metode_pelunasan' => 'required',
        ]);
        if ($validator->passes()) {

            $period = new DatePeriod(
                new DateTime($request['keberangkatan']),
                new DateInterval('P1D'),
                new DateTime($request['kepulangan'] . '+1 day')
            );

            DB::beginTransaction();
            try {
                if ($request['metode_pelunasan'] == 'transfer') {
                    $imgTrf = $request->file('bukti_pelunasan')->store('buktiPelunasan', 'public');
                } else {
                    $imgTrf = '';
                }
                $data = Transaksi::find($id);

                $data->update([
                    'kepulangan' => $request['kepulangan'],
                    'metode_pelunasan' => $request['metode_pelunasan'],
                    'status' => 'selesai',
                    'over_time' => $request['over_time'],
                    'biaya' => $request['biaya'],
                    'sisa' => $request['sisa'],
                    'bukti_pelunasan' => $imgTrf,
                ]);

                // foreach ($period as $key => $value) {
                //     RangeTransaksi::create([
                //         'id_transaksi' => $data->id,
                //         'id_kendaraan' => $request['id_kendaraan'],
                //         'tanggal' => $value->format('Y-m-d'),
                //     ]);
                // }

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

            $period = new DatePeriod(
                new DateTime($request['keberangkatan']),
                new DateInterval('P1D'),
                new DateTime($request['kepulangan'] . '+1 day')
            );

            DB::beginTransaction();
            try {
                if ($request['metode_pelunasan'] == 'transfer') {
                    $imgTrf = $request->file('bukti_pelunasan')->store('buktiPelunasan', 'public');
                } else {
                    $imgTrf = '';
                }
                $data = Transaksi::find($id);

                $data->update([
                    'kepulangan' => $request['kepulangan'],
                    'metode_pelunasan' => $request['metode_pelunasan'],
                    'status' => 'selesai',
                    'over_time' => $request['over_time'],
                    'biaya' => $request['biaya'],
                    'sisa' => $request['sisa'],
                    'bukti_pelunasan' => $imgTrf,
                ]);

                // foreach ($period as $key => $value) {
                //     RangeTransaksi::create([
                //         'id_transaksi' => $data->id,
                //         'id_kendaraan' => $request['id_kendaraan'],
                //         'tanggal' => $value->format('Y-m-d'),
                //     ]);
                // }

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
