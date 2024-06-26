<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Helpers\SettingWeb;
use App\Models\Kendaraan;
use App\Models\HargaKendaraan;
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
            'estimasi_tgl' => 'required',
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
                    'estimasi_tgl' => $request['estimasi_tgl'],
                    'estimasi_time' => $request['estimasi_time'],
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

                $dataWa = Transaksi::select('transaksi.id', 'transaksi.estimasi_tgl', 'transaksi.keberangkatan_time', 'transaksi.harga_sewa', 'penyewa.nama', 'penyewa.no_hp', 'kendaraan.no_kendaraan', 'jenis.nama as jenis', 'kendaraan.warna', 'transaksi.lama_sewa', 'transaksi.biaya', 'transaksi.sisa')
                    ->selectRaw('(select SUM(CASE WHEN pembayaran.nominal THEN pembayaran.nominal ELSE 0 END) as uang_masuk from pembayaran WHERE pembayaran.id_transaksi = transaksi.id) as uang_masuk')
                    ->join('kendaraan', 'kendaraan.id', '=', 'transaksi.id_kendaraan')
                    ->join('penyewa', 'penyewa.id', '=', 'transaksi.id_penyewa')
                    ->join('jenis', 'kendaraan.id_jenis', '=', 'jenis.id')
                    ->where('transaksi.id', $data->id)
                    ->first();
                // dd($dataWa->toArray());
                $response = response()->json($this->responseStore(true, NULL, "https://api.whatsapp.com/send/?phone=" . $dataWa->no_hp . "&text=" . SettingWeb::get_setting()->header_inv . "%0a=========================%0aTgl%20Penyewaan%20:%" . $dataWa->estimasi_tgl . "%0aNo%20Kwitansi%20:%20" . $dataWa->id . "%0aNama%20:%20" . $dataWa->nama . "%0a=========================%0aSewa%20Mobil%20%0aNo%20Kendaraan%20:%20" . $dataWa->no_kendaraan . "%0aJenis%20:%20" . $dataWa->jenis . "%20" . $dataWa->warna . "%0a=========================%0aUang%20Masuk%20:%20Rp.%20" .  number_format($dataWa->uang_masuk) . "%0a(Per%20tanggal%20:%20" . date("Y-m-d H:i") . ")%0a=========================%0a" . SettingWeb::get_setting()->footer_inv));
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

        $start = Carbon::parse();
        $end =  Carbon::parse();
        $duration = $end->diff($start);
        $hari = $end->diffInDays($start);
        $jam = $duration->format('%H');

        $get_harga = HargaKendaraan::SELECT('harga_kendaraan.id', 'harga.nilai', 'harga.nominal')
            ->JOIN('harga', 'harga.id', '=', 'harga_kendaraan.id_harga')
            ->WHERE('harga_kendaraan.id_kendaraan', $data->id_kendaraan)
            ->WHERE('harga.nilai', '>=', $jam)
            ->ORDERBY('harga.nilai', 'ASC')
            ->first();

        $harga = $get_harga->nominal;

        return view('backend.pemesanan.proses', compact('config', 'data', 'pembayaran', 'hari', 'jam', 'harga'));
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
            'estimasi_tgl' => 'required',
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
                    'estimasi_tgl' => $request['estimasi_tgl'],
                    'estimasi_time' => $request['estimasi_time'],
                    'diskon' => 0,
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
                $dataWa = Transaksi::select('transaksi.id', 'transaksi.estimasi_tgl', 'transaksi.keberangkatan_time', 'transaksi.harga_sewa', 'penyewa.nama', 'penyewa.no_hp', 'kendaraan.no_kendaraan', 'jenis.nama as jenis', 'kendaraan.warna', 'transaksi.lama_sewa', 'transaksi.biaya', 'transaksi.sisa')
                    ->selectRaw('(select SUM(CASE WHEN pembayaran.nominal THEN pembayaran.nominal ELSE 0 END) as uang_masuk from pembayaran WHERE pembayaran.id_transaksi = transaksi.id) as uang_masuk')
                    ->join('kendaraan', 'kendaraan.id', '=', 'transaksi.id_kendaraan')
                    ->join('penyewa', 'penyewa.id', '=', 'transaksi.id_penyewa')
                    ->join('jenis', 'kendaraan.id_jenis', '=', 'jenis.id')
                    ->where('transaksi.id', $data->id)
                    ->first();
                // dd($dataWa->toArray());
                $response = response()->json($this->responseStore(true, NULL, "https://api.whatsapp.com/send/?phone=" . $dataWa->no_hp . "&text=" . SettingWeb::get_setting()->header_inv . "%0a=========================%0aTgl%20Penyewaan%20:%" . $dataWa->estimasi_tgl . "%0aNo%20Kwitansi%20:%20" . $dataWa->id . "%0aNama%20:%20" . $dataWa->nama . "%0a=========================%0aSewa%20Mobil%20%0aNo%20Kendaraan%20:%20" . $dataWa->no_kendaraan . "%0aJenis%20:%20" . $dataWa->jenis . "%20" . $dataWa->warna . "%0a=========================%0aUang%20Masuk%20:%20Rp.%20" .  number_format($dataWa->uang_masuk) . "%0a(Per%20tanggal%20:%20" . date("Y-m-d H:i") . ")%0a=========================%0a" . SettingWeb::get_setting()->footer_inv));
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
            'status' => 'required',
            'hari' =>  $request['status'] == 'proses' ? 'required' : '',
            'jam' =>  $request['status'] == 'proses' ? 'required' : '',
            'jaminan' =>  $request['status'] == 'proses' ? 'required' : '',
            'harga_sewa' =>  $request['status'] == 'proses' ? 'required' : '',
            'estimasi_sewa' =>  $request['status'] == 'proses' ? 'required' : '',
            'biaya' =>  $request['status'] == 'proses' ? 'required' : '',
            'sisa' =>  $request['status'] == 'proses' ? 'required' : '',
            'file.*' => 'mimes:jpg,png,jpeg,gif,svg|max:2048',
            'kota_tujuan' => $request['status'] == 'proses' ? 'required' : '',
        ]);
        if ($validator->passes()) {
            $cekMobil = Transaksi::select('id')->where('id_kendaraan', $request['id_kendaraan'])->where('status', '=', 'proses')->first();
            if ($cekMobil == null) {
                DB::beginTransaction();
                try {

                    $keberangkatan_time = Carbon::now();
                    $keberangkatan = Carbon::today();

                    $data = Transaksi::find($id);

                    if (isset($request['hari'])) {
                        $hari = $request['hari'];
                    } else {
                        $hari = 0;
                    }


                    if (isset($request['jam']) && $request['jam'] <= 6) {
                        $jam = 6;
                    } else {
                        $jam = isset($request['jam']) ? $request['jam'] : 0;
                    }

                    $d = $hari . " Hari " . $jam . " Jam";

                    $data->update([
                        'id_penyewa' => $request['id_penyewa'],
                        'id_kendaraan' => $request['id_kendaraan'],
                        'keberangkatan' => $keberangkatan,
                        'keberangkatan_time' => $keberangkatan_time,
                        'status' => $request['status'],
                        'kota_tujuan' => $request['status'] == 'proses' ?  $request['kota_tujuan'] : null,
                        'tipe' => $request['status'] == 'proses' ? 'sewa' : 'pesan',
                        'status' => $request['status'],
                        'jaminan' => $request['status'] == 'proses' ? $request['jaminan'] : null,
                        'harga_sewa' => $request['status'] == 'proses' ? $request['harga_sewa'] : null,
                        'estimasi_sewa' => $request['status'] == 'proses' ? $request['estimasi_sewa'] : null,
                        'biaya' => $request['status'] == 'proses' ? $request['biaya'] : null,
                        'sisa' => $request['status'] == 'proses' ? $request['sisa'] : null,
                        'durasi' => $d,
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

                    $dataWa = Transaksi::select('transaksi.id', 'transaksi.keberangkatan', 'transaksi.keberangkatan_time', 'transaksi.harga_sewa', 'penyewa.nama', 'penyewa.no_hp', 'kendaraan.no_kendaraan', 'jenis.nama as jenis', 'kendaraan.warna', 'transaksi.lama_sewa', 'transaksi.estimasi_sewa', 'transaksi.biaya', 'transaksi.sisa', 'transaksi.durasi')
                        ->selectRaw('(select SUM(CASE WHEN pembayaran.nominal THEN pembayaran.nominal ELSE 0 END) as uang_masuk from pembayaran WHERE pembayaran.id_transaksi = transaksi.id) as uang_masuk')
                        ->join('kendaraan', 'kendaraan.id', '=', 'transaksi.id_kendaraan')
                        ->join('penyewa', 'penyewa.id', '=', 'transaksi.id_penyewa')
                        ->join('jenis', 'kendaraan.id_jenis', '=', 'jenis.id')
                        ->where('transaksi.id', $data->id)
                        ->first();
                    $response = response()->json($this->responseStore(true, NULL, "https://api.whatsapp.com/send/?phone=" . $dataWa->no_hp . "&text=" . SettingWeb::get_setting()->header_inv . "%0a=========================%0aTgl%20Penyewaan%20:%" . $dataWa->keberangkatan . "%20" . $dataWa->keberangkatan_time . "%0aNo%20Kwitansi%20:%20" . $dataWa->id . "%0aNama%20:%20" . $dataWa->nama . "%0a=========================%0aSewa%20Mobil%20%0aNo%20Kendaraan%20:%20" . $dataWa->no_kendaraan . "%0aJenis%20:%20" . $dataWa->jenis . "%20" . $dataWa->warna . "%0aEstimasi%20lama%20Sewa%20:%20" . $dataWa->estimasi_sewa . "%0a=========================%0aHarga%20Sewa%20:%20Rp.%20" . number_format($dataWa->harga_sewa) . "%20(%20" . $dataWa->durasi . "%20)%20%0aUang%20Masuk%20:%20Rp.%20" .  number_format($dataWa->uang_masuk) . "%0aTotal%20:%20Rp.%20" . number_format($dataWa->biaya) . "%0aSisa%20Belum%20Terbayar%20:%20Rp.%20" . number_format($dataWa->sisa) . "%0a(Per%20tanggal%20:%20" . date("Y-m-d H:i") . ")%0a=========================%0a" . SettingWeb::get_setting()->footer_inv));
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
