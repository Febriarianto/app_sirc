<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Helpers\SettingWeb;
use App\Models\Kendaraan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\RangeTransaksi;
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
            $data = Transaksi::with('penyewa', 'kendaraan')->where(['tipe' => 'pesan', 'status' => 'pending'])->get();

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
            'estimasi_time' => 'required',
            'file.*' => 'mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {
                $today  = date('Y-m-d');

                $get_id = Transaksi::select('id')->whereDate('created_at', $today)->get();
                $count = $get_id->count();

                $no_inv = 'INV-' . date('Ymd') . '-' . $count + 1;

                $data = Transaksi::create([
                    'no_inv' => $no_inv,
                    'id_penyewa' => $request['id_penyewa'],
                    'id_kendaraan' => $request['id_kendaraan'],
                    'keberangkatan' => $request['keberangkatan'],
                    'estimasi_time' => $request['estimasi_time'],
                    'harga_sewa' => $request['harga_sewa'],
                    'paket' => 'harian',
                    'tipe' => 'pesan',
                    'status' => 'pending',
                ]);

                if (isset($request->tipe)) {
                    foreach ($request->tipe as $key => $t) {
                        if ($request->metode[$key] == 'transfer') {
                            $imgTrf = date("Y-m-d") . '_' . $request->file[$key]->getClientOriginalName();
                            $request->file[$key]->storeAs('public/buktiTrf/', $imgTrf);
                        } else {
                            $imgTrf = '';
                        }

                        Pembayaran::create([
                            'id_transaksi' => $data->id,
                            'tipe' => $t,
                            'nominal' => $request->nominal[$key],
                            'metode' => $request->metode[$key],
                            'file' => $imgTrf,
                            'detail' => 'Pemesanan',
                            'penerima' => Auth()->user()->name,
                        ]);
                    }
                }

                DB::commit();

                $dataWa = Transaksi::select('transaksi.id', 'transaksi.keberangkatan', 'transaksi.keberangkatan_time', 'transaksi.harga_sewa', 'penyewa.nama', 'penyewa.no_hp', 'kendaraan.no_kendaraan', 'jenis.nama as jenis', 'kendaraan.warna', 'transaksi.lama_sewa', 'transaksi.biaya', 'transaksi.sisa')
                    ->selectRaw('(select SUM(CASE WHEN pembayaran.nominal THEN pembayaran.nominal ELSE 0 END) as uang_masuk from pembayaran WHERE pembayaran.id_transaksi = transaksi.id) as uang_masuk')
                    ->join('kendaraan', 'kendaraan.id', '=', 'transaksi.id_kendaraan')
                    ->join('penyewa', 'penyewa.id', '=', 'transaksi.id_penyewa')
                    ->join('jenis', 'kendaraan.id_jenis', '=', 'jenis.id')
                    ->where('transaksi.id', $data->id)
                    ->first();
                // dd($dataWa->toArray());
                $response = response()->json($this->responseStore(true, NULL, "https://api.whatsapp.com/send/?phone=" . $dataWa->no_hp . "&text=" . SettingWeb::get_setting()->header_inv . "%0a=========================%0aTgl%20Penyewaan%20:%" . $dataWa->keberangkatan . "%0aNo%20Kwitansi%20:%20" . $dataWa->id . "%0aNama%20:%20" . $dataWa->nama . "%0a=========================%0aSewa%20Mobil%20%0aNo%20Kendaraan%20:%20" . $dataWa->no_kendaraan . "%0aJenis%20:%20" . $dataWa->jenis . "%20" . $dataWa->warna . "%0a=========================%0aHarga%20Sewa%20:%20Rp.%20" . number_format($dataWa->harga_sewa) . "%0aUang%20Masuk%20:%20Rp.%20" .  number_format($dataWa->uang_masuk) . "%0a(Per%20tanggal%20:%20" . date("Y-m-d H:i") . ")%0a=========================%0a" . SettingWeb::get_setting()->footer_inv));
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
        $pembayaran = Pembayaran::where('id_transaksi', $id)->get();
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('pemesanan.proses', $id)
        ];
        return view('backend.pemesanan.proses', compact('config', 'data', 'pembayaran'));
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
        $pembayaran = Pembayaran::where('id_transaksi', $id)->get();
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('pemesanan.update', $id)
        ];
        return view('backend.pemesanan.form', compact('config', 'data', 'pembayaran'));
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
            'estimasi_time' => 'required',
            'file.*' => 'mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {

                $data = Transaksi::find($id);

                $data->update([
                    'id_penyewa' => $request['id_penyewa'],
                    'id_kendaraan' => $request['id_kendaraan'],
                    'keberangkatan' => $request['keberangkatan'],
                    'estimasi_time' => $request['estimasi_time'],
                    'harga_sewa' => $request['harga_sewa'],
                    'tipe' => 'pesan',
                    'status' => 'pending',
                ]);

                // if (isset($request->idP)) {
                //     foreach ($request->idP as $key => $p) {
                //         $dataP = Pembayaran::find($p);

                //         if ($request->metodeP[$key] == 'transfer' && $request->fileP !== NULL) {
                //             $imgTrfP = date("Y-m-d") . '_' . $request->fileP[$key]->getClientOriginalName();
                //             $request->fileP[$key]->storeAs('public/buktiTrf/', $imgTrfP);
                //         } else {
                //             $imgTrfP = $dataP->file;
                //         }

                //         $dataP->update([
                //             'id_transaksi' => $data->id,
                //             'tipe' => $request->tipeP[$key],
                //             'nominal' => $request->nominalP[$key],
                //             'metode' => $request->metodeP[$key],
                //             'file' => $imgTrfP,
                //             'penerima' => Auth()->user()->name,
                //         ]);
                //     }
                // }

                if (isset($request->tipe)) {
                    foreach ($request->tipe as $key => $t) {
                        if ($request->metode[$key] == 'transfer') {
                            $imgTrf = date("Y-m-d") . '_' . $request->file[$key]->getClientOriginalName();
                            $request->file[$key]->storeAs('public/buktiTrf/', $imgTrf);
                        } else {
                            $imgTrf = '';
                        }

                        Pembayaran::create([
                            'id_transaksi' => $data->id,
                            'tipe' => $t,
                            'nominal' => $request->nominal[$key],
                            'metode' => $request->metode[$key],
                            'file' => $imgTrf,
                            'detail' => 'Pemesanan',
                            'penerima' => Auth()->user()->name,
                        ]);
                    }
                }

                DB::commit();
                $dataWa = Transaksi::select('transaksi.id', 'transaksi.keberangkatan', 'transaksi.keberangkatan_time', 'transaksi.harga_sewa', 'penyewa.nama', 'penyewa.no_hp', 'kendaraan.no_kendaraan', 'jenis.nama as jenis', 'kendaraan.warna', 'transaksi.lama_sewa', 'transaksi.biaya', 'transaksi.sisa')
                    ->selectRaw('(select SUM(CASE WHEN pembayaran.nominal THEN pembayaran.nominal ELSE 0 END) as uang_masuk from pembayaran WHERE pembayaran.id_transaksi = transaksi.id) as uang_masuk')
                    ->join('kendaraan', 'kendaraan.id', '=', 'transaksi.id_kendaraan')
                    ->join('penyewa', 'penyewa.id', '=', 'transaksi.id_penyewa')
                    ->join('jenis', 'kendaraan.id_jenis', '=', 'jenis.id')
                    ->where('transaksi.id', $data->id)
                    ->first();
                // dd($dataWa->toArray());
                $response = response()->json($this->responseStore(true, NULL, "https://api.whatsapp.com/send/?phone=" . $dataWa->no_hp . "&text=" . SettingWeb::get_setting()->header_inv . "%0a=========================%0aTgl%20Penyewaan%20:%" . $dataWa->keberangkatan . "%0aNo%20Kwitansi%20:%20" . $dataWa->id . "%0aNama%20:%20" . $dataWa->nama . "%0a=========================%0aSewa%20Mobil%20%0aNo%20Kendaraan%20:%20" . $dataWa->no_kendaraan . "%0aJenis%20:%20" . $dataWa->jenis . "%20" . $dataWa->warna . "%0a=========================%0aHarga%20Sewa%20:%20Rp.%20" . number_format($dataWa->harga_sewa) . "%0aUang%20Masuk%20:%20Rp.%20" .  number_format($dataWa->uang_masuk) . "%0a(Per%20tanggal%20:%20" . date("Y-m-d H:i") . ")%0a=========================%0a" . SettingWeb::get_setting()->footer_inv));
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
            'id_penyewa' => 'required',
            'id_kendaraan' => 'required',
            'keberangkatan' => 'required',
            'status' => 'required',
            'harga_sewa' => 'required',
            'jaminan' =>  $request['status'] == 'proses' ? 'required' : '',
            'file.*' => 'mimes:jpg,png,jpeg,gif,svg|max:2048',
            'lama_sewa' => $request['status'] == 'proses' ? 'required' : '',
            'paket' => $request['status'] == 'proses' ? 'required' : '',
            'kota_tujuan' => $request['status'] == 'proses' ? 'required' : '',
            'biaya' => $request['status'] == 'proses' ? 'required' : '',
            'sisa' => $request['status'] == 'proses' ? 'required' : '',
        ]);
        if ($validator->passes()) {
            $cekMobil = Transaksi::select('id')->where('id_kendaraan', $request['id_kendaraan'])->where('status', '=', 'proses')->first();
            if ($cekMobil == null) {
                DB::beginTransaction();
                try {

                    $keberangkatan_time = Carbon::now();
                    $keberangkatan = Carbon::today();

                    $data = Transaksi::find($id);

                    $data->update([
                        'id_penyewa' => $request['id_penyewa'],
                        'id_kendaraan' => $request['id_kendaraan'],
                        'keberangkatan' => $request['status'] == 'proses' ? $keberangkatan : $request['keberangkatan'],
                        'keberangkatan_time' => $keberangkatan_time,
                        'status' => $request['status'],
                        'lama_sewa' => $request['lama_sewa'],
                        'harga_sewa' => $request['harga_sewa'],
                        'paket' => $request['status'] == 'proses' ? $request['paket'] : 'harian',
                        'kota_tujuan' => $request['status'] == 'proses' ?  $request['kota_tujuan'] : null,
                        'biaya' => $request['status'] == 'proses' ? $request['biaya'] : null,
                        'sisa' => $request['status'] == 'proses' ? $request['sisa'] : null,
                        'tipe' => $request['status'] == 'proses' ? 'sewa' : 'pesan',
                        'status' => $request['status'],
                        'jaminan' => $request['status'] == 'proses' ? $request['jaminan'] : null,
                    ]);

                    // if (isset($request->idP)) {
                    //     foreach ($request->idP as $key => $p) {
                    //         $dataP = Pembayaran::find($p);

                    //         if ($request->metodeP[$key] == 'transfer' && $request->fileP !== NULL) {
                    //             $imgTrfP = date("Y-m-d") . '_' . $request->fileP[$key]->getClientOriginalName();
                    //             $request->fileP[$key]->storeAs('public/buktiTrf/', $imgTrfP);
                    //         } else {
                    //             $imgTrfP = $dataP->file;
                    //         }

                    //         $dataP->update([
                    //             'id_transaksi' => $data->id,
                    //             'tipe' => $request->tipeP[$key],
                    //             'nominal' => $request->nominalP[$key],
                    //             'metode' => $request->metodeP[$key],
                    //             'file' => $imgTrfP,
                    //             'penerima' => Auth()->user()->name,
                    //         ]);
                    //     }
                    // }

                    if (isset($request->tipe)) {
                        foreach ($request->tipe as $key => $t) {
                            if ($request->metode[$key] == 'transfer') {
                                $imgTrf = date("Y-m-d") . '_' . $request->file[$key]->getClientOriginalName();
                                $request->file[$key]->storeAs('public/buktiTrf/', $imgTrf);
                            } else {
                                $imgTrf = '';
                            }

                            Pembayaran::create([
                                'id_transaksi' => $data->id,
                                'tipe' => $t,
                                'nominal' => $request->nominal[$key],
                                'metode' => $request->metode[$key],
                                'file' => $imgTrf,
                                'detail' => 'Penyewaan',
                                'penerima' => Auth()->user()->name,
                            ]);
                        }
                    }

                    if ($request['status'] == 'proses') {
                        $period = new DatePeriod(
                            new DateTime($request['keberangkatan']),
                            new DateInterval('P1D'),
                            new DateTime($request['keberangkatan'] . '+' . ($request['lama_sewa'] + 1) . ' day')
                        );

                        foreach ($period as $key => $value) {
                            RangeTransaksi::create([
                                'id_transaksi' => $data->id,
                                'id_kendaraan' => $request['id_kendaraan'],
                                'tanggal' => $value->format('Y-m-d'),
                            ]);
                        }
                    }

                    DB::commit();

                    $dataWa = Transaksi::select('transaksi.id', 'transaksi.keberangkatan', 'transaksi.keberangkatan_time', 'transaksi.harga_sewa', 'penyewa.nama', 'penyewa.no_hp', 'kendaraan.no_kendaraan', 'jenis.nama as jenis', 'kendaraan.warna', 'transaksi.lama_sewa', 'transaksi.biaya', 'transaksi.sisa')
                        ->join('kendaraan', 'kendaraan.id', '=', 'transaksi.id_kendaraan')
                        ->join('penyewa', 'penyewa.id', '=', 'transaksi.id_penyewa')
                        ->join('jenis', 'kendaraan.id_jenis', '=', 'jenis.id')
                        ->where('transaksi.id', $data->id)
                        ->first();
                    // dd($dataWa->toArray());
                    $response = response()->json($this->responseStore(true, NULL, "https://api.whatsapp.com/send/?phone=" . $dataWa->no_hp . "&text=" . SettingWeb::get_setting()->header_inv . "%0a=========================%0aTgl%20Penyewaan%20:%" . $dataWa->keberangkatan . "%20" . $dataWa->keberangkatan_time . "%0aNo%20Kwitansi%20:%20" . $dataWa->id . "%0aNama%20:%20" . $dataWa->nama . "%0a=========================%0aSewa%20Mobil%20%0aNo%20Kendaraan%20:%20" . $dataWa->no_kendaraan . "%0aJenis%20:%20" . $dataWa->jenis . "%20" . $dataWa->warna . "%0alama%20Sewa%20:%20" . $dataWa->lama_sewa . "%20Hari%0a=========================%0aHarga%20Sewa%20:%20Rp.%20" . number_format($dataWa->harga_sewa) . "%0aUang%20Masuk%20:%20Rp.%20" .  number_format($dataWa->biaya - $data->sisa) . "%0aTotal%20:%20Rp.%20" . number_format($dataWa->biaya) . "%0aSisa%20Belum%20Terbayar%20:%20Rp.%20" . number_format($dataWa->sisa) . "%0a(Per%20tanggal%20:%20" . date("Y-m-d H:i") . ")%0a=========================%0a" . SettingWeb::get_setting()->footer_inv));
                } catch (\Throwable $throw) {
                    DB::rollBack();
                    Log::error($throw);
                    $response = response()->json(['error' => $throw->getMessage()]);
                }
            } else {
                $response = response()->json([
                    'status' => 'error',
                    'message' => 'Mobil Sedang Di Sewa'
                ]);
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
