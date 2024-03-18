<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Helpers\SettingWeb;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\RangeTransaksi;
use App\Models\Pembayaran;
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
                ->addColumn('hari', function ($row) {
                    $waktustart = $row->keberangkatan . " " . $row->keberangkatan_time;
                    $waktuend = date("Y-m-d h:i:s");
                    $datetime1 = new \DateTime($waktustart); //start time
                    $datetime2 = new \DateTime($waktuend); //end time
                    $durasi = $datetime1->diff($datetime2);
                    // if ($durasi->format('%y') !== '0') {
                    //     return $durasi->format('%y tahun, %m bulan, %d hari, %H jam');
                    // } elseif ($durasi->format('%m') !== '0') {
                    //     return $durasi->format('%m bulan, %d hari, %H jam');
                    // } else {
                    //     return $durasi->format('%d hari, %H jam');
                    // }
                    return $durasi->format('%d');
                })
                ->addColumn('jam', function ($row) {
                    $waktustart = $row->keberangkatan . " " . $row->keberangkatan_time;
                    $waktuend = date("Y-m-d h:i:s");
                    $datetime1 = new \DateTime($waktustart); //start time
                    $datetime2 = new \DateTime($waktuend); //end time
                    $durasi = $datetime1->diff($datetime2);
                    return $durasi->format('%H');
                })
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

        $dataTransaksi = Kendaraan::where('id', $id_kendaraan)
            ->with(['jenis'])
            ->first();
        return view('backend.penyewaan.form', compact('config', 'kendaraan', 'dataTransaksi',));
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
            'harga_sewa' => 'required',
            'lama_sewa' => 'required',
            'paket' => 'required',
            'kota_tujuan' => 'required',
            'biaya' => 'required',
            'diskon' => 'required',
            'sisa' => 'required',
            'jaminan' => 'required',
            'file.*' => 'mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {
                $today  = date('Y-m-d');

                $get_id = Transaksi::select('id')->whereDate('created_at', $today)->get();
                $count = $get_id->count();

                $no_inv = 'INV-' . date('Ymd') . '-' . $count + 1;

                $keberangkatan_time = Carbon::now();

                $data = Transaksi::create([
                    'no_inv' => $no_inv,
                    'id_penyewa' => $request['id_penyewa'],
                    'id_kendaraan' => $request['id_kendaraan'],
                    'keberangkatan' => $request['keberangkatan'],
                    'keberangkatan_time' => $keberangkatan_time,
                    'status' => 'proses',
                    'lama_sewa' => $request['lama_sewa'],
                    'harga_sewa' => $request['harga_sewa'],
                    'diskon' => $request['diskon'],
                    'paket' => $request['paket'],
                    'kota_tujuan' => $request['kota_tujuan'],
                    'biaya' => $request['biaya'],
                    'sisa' => $request['sisa'],
                    'jaminan' => $request['jaminan'],
                    'tipe' => 'sewa',
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
                            'detail' => 'Penyewaan',
                            'penerima' => Auth()->user()->name,
                        ]);
                    }
                }

                // save periode

                if ($request['paket'] == 'jam') {
                    $period = new DatePeriod(
                        new DateTime($request['keberangkatan']),
                        new DateInterval('P1D'),
                        new DateTime($request['keberangkatan'] . '+1 day')
                    );
                } else {
                    $period = new DatePeriod(
                        new DateTime($request['keberangkatan']),
                        new DateInterval('P1D'),
                        new DateTime($request['keberangkatan'] . '+' . ($request['lama_sewa'] + 1) . ' day')
                    );
                }

                foreach ($period as $key => $value) {
                    RangeTransaksi::create([
                        'id_transaksi' => $data->id,
                        'id_kendaraan' => $request['id_kendaraan'],
                        'tanggal' => $value->format('Y-m-d'),
                    ]);
                }

                DB::commit();

                $dataWa = Transaksi::select('transaksi.id', 'transaksi.keberangkatan', 'transaksi.keberangkatan_time', 'transaksi.harga_sewa', 'penyewa.nama', 'penyewa.no_hp', 'kendaraan.no_kendaraan', 'jenis.nama as jenis', 'kendaraan.warna', 'transaksi.lama_sewa', 'transaksi.biaya', 'transaksi.sisa')
                    ->join('kendaraan', 'kendaraan.id', '=', 'transaksi.id_kendaraan')
                    ->join('penyewa', 'penyewa.id', '=', 'transaksi.id_penyewa')
                    ->join('jenis', 'kendaraan.id_jenis', '=', 'jenis.id')
                    ->where('transaksi.id', $data->id)
                    ->first();
                $response = response()->json($this->responseStore(true, NULL, "https://api.whatsapp.com/send/?phone=" . $dataWa->no_hp . "&text=" . SettingWeb::get_setting()->header_inv . "%0a=========================%0aTgl%20Penyewaan%20:%" . $dataWa->keberangkatan . "%20" . $dataWa->keberangkatan_time . "%0aNo%20Kwitansi%20:%20" . $dataWa->id . "%0aNama%20:%20" . $dataWa->nama . "%0a=========================%0aSewa%20Mobil%20%0aNo%20Kendaraan%20:%20" . $dataWa->no_kendaraan . "%0aJenis%20:%20" . $dataWa->jenis . "%20" . $dataWa->warna . "%0alama%20Sewa%20:%20" . $dataWa->lama_sewa . "%20Hari%0a=========================%0aHarga%20Sewa%20:%20Rp.%20" . number_format($dataWa->harga_sewa) . "%0aUang%20Masuk%20:%20Rp.%20" .  number_format($dataWa->biaya - $data->sisa) . "%0aTotal%20:%20Rp.%20" . number_format($dataWa->biaya) . "%0aSisa%20Belum%20Terbayar%20:%20Rp.%20" . number_format($dataWa->sisa) . "%0a(Per%20tanggal%20:%20" . date("Y-m-d H:i") . ")%0a=========================%0a" . SettingWeb::get_setting()->footer_inv));
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
        $config['title'] = "Cetak Invoice";
        $config['breadcrumbs'] = [
            ['url' => route('invoice.index'), 'title' => "Cetak"],
            ['url' => '#', 'title' => "Proses Pemyewaan"],
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
    public function edit($id, $id_kendaraan)
    {
        $config['title'] = "Edit Penyewaan";
        $config['breadcrumbs'] = [
            ['url' => route('penyewaan.index'), 'title' => "Penyewaan"],
            ['url' => '#', 'title' => "Edit Pemesan"],
        ];

        $data = Transaksi::with(['penyewa', 'kendaraan'])->where('id', $id)->first();
        $pembayaran = Pembayaran::where('id_transaksi', $id)->get();
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('penyewaan.update', $id)
        ];

        return view('backend.penyewaan.form', compact('config', 'data', 'pembayaran'));
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
            'harga_sewa' => 'required',
            'diskon' => 'required',
            'lama_sewa' => 'required',
            'paket' => 'required',
            'kota_tujuan' => 'required',
            'biaya' => 'required',
            'sisa' => 'required',
            'jaminan' => 'required',
            'file.*' => 'mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {

                $data = Transaksi::find($id);

                if ((int)$request['id_kendaraan'] !== $data->id_kendaraan) {
                    $dtRange = RangeTransaksi::select('id')->where('id_kendaraan', $data->id_kendaraan)->get();
                    foreach ($dtRange as $dtR) {
                        $dataOld = RangeTransaksi::find($dtR->id);
                        $dataOld->update([
                            'id_kendaraan' => $request['id_kendaraan']
                        ]);
                    }
                }

                if ($data->lama_sewa !== $request['lama_sewa']) {
                    $perpanjang = 'Y';
                } elseif ($data->paket !== $request['paket']) {
                    $perpanjang = 'Y';
                } else {
                    $perpanjang = 'N';
                }

                $data->update([
                    'id_penyewa' => $request['id_penyewa'],
                    'id_kendaraan' => $request['id_kendaraan'],
                    'keberangkatan' => $request['keberangkatan'],
                    'status' => 'proses',
                    'lama_sewa' => $request['lama_sewa'],
                    'harga_sewa' => $request['harga_sewa'],
                    'diskon' => $request['diskon'],
                    'paket' => $request['paket'],
                    'kota_tujuan' => $request['kota_tujuan'],
                    'biaya' => $request['biaya'],
                    'sisa' => $request['sisa'],
                    'jaminan' => $request['jaminan'],
                    'tipe' => 'sewa',
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
                            'detail' => 'Penyewaan',
                            'penerima' => Auth()->user()->name,
                        ]);
                    }
                }

                if ($perpanjang == 'Y') {
                    $dataTgl = RangeTransaksi::where('id_transaksi', $data->id)->delete();

                    // save periode

                    if ($request['paket'] == 'jam') {
                        $period = new DatePeriod(
                            new DateTime($request['keberangkatan']),
                            new DateInterval('P1D'),
                            new DateTime($request['keberangkatan'] . '+1 day')
                        );
                    } else {
                        $period = new DatePeriod(
                            new DateTime($request['keberangkatan']),
                            new DateInterval('P1D'),
                            new DateTime($request['keberangkatan'] . '+' . ($request['lama_sewa'] + 1) . ' day')
                        );
                    }

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
            $response = response()->json(['error' => $validator->errors()->all()]);
        }
        return $response;
    }

    public function proses(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'over_time' => 'required',
            'biaya' => 'required',
            'sisa' => 'required',
            'file.*' => 'mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {

                $data = Transaksi::find($id);

                if ($request['sisa'] !== "0") {
                    $ket = "belum lunas";
                } else {
                    $ket = "lunas";
                }

                $data->update([
                    'over_time' => $request['over_time'],
                    'biaya' => $request['biaya'],
                    'sisa' => $request['sisa'],
                    'keterangan' => $ket,
                    'status' => 'selesai',
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
                            'detail' => 'Penyewaan',
                            'penerima' => Auth()->user()->name,
                        ]);
                    }
                }

                $dataTgl = RangeTransaksi::where('id_transaksi', $data->id)->delete();

                DB::commit();
                $dataWa = Transaksi::select('transaksi.id', 'transaksi.keberangkatan', 'transaksi.keberangkatan_time', 'transaksi.harga_sewa', 'penyewa.nama', 'penyewa.no_hp', 'kendaraan.no_kendaraan', 'jenis.nama as jenis', 'kendaraan.warna', 'transaksi.lama_sewa', 'transaksi.biaya', 'transaksi.sisa')
                    ->join('kendaraan', 'kendaraan.id', '=', 'transaksi.id_kendaraan')
                    ->join('penyewa', 'penyewa.id', '=', 'transaksi.id_penyewa')
                    ->join('jenis', 'kendaraan.id_jenis', '=', 'jenis.id')
                    ->where('transaksi.id', $data->id)
                    ->first();

                $response = response()->json($this->responseStore(true, NULL, "https://api.whatsapp.com/send/?phone=" . $dataWa->no_hp . "&text=" . SettingWeb::get_setting()->header_inv . "%0a=========================%0aTgl%20Penyewaan%20:%" . $dataWa->keberangkatan . "%20" . $dataWa->keberangkatan_time . "%0aNo%20Kwitansi%20:%20" . $dataWa->id . "%0aNama%20:%20" . $dataWa->nama . "%0a=========================%0aSewa%20Mobil%20%0aNo%20Kendaraan%20:%20" . $dataWa->no_kendaraan . "%0aJenis%20:%20" . $dataWa->jenis . "%20" . $dataWa->warna . "%0alama%20Sewa%20:%20" . $dataWa->lama_sewa . "%20Hari%0a=========================%0aHarga%20Sewa%20:%20Rp.%20" . number_format($dataWa->harga_sewa) . "%0aUang%20Masuk%20:%20Rp.%20" .  number_format($dataWa->biaya - $data->sisa) . "%0aTotal%20:%20Rp.%20" . number_format($dataWa->biaya) . "%0aSisa%20Belum%20Terbayar%20:%20Rp.%20" . number_format($dataWa->sisa) . "%0a(Per%20tanggal%20:%20" . date("Y-m-d H:i") . ")%0a=========================%0a" . SettingWeb::get_setting()->footer_inv));
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
