<?php

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use App\Models\Jenis;
use App\Models\Kendaraan;
use App\Models\HargaKendaraan;
use Illuminate\Http\Request;
use App\Traits\ResponseStatus;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Picqer\Barcode\BarcodeGeneratorPNG;
use DateTime;

class KendaraanController extends Controller
{
    use ResponseStatus;

    function __construct()
    {
        $this->middleware('can:kendaraan-list', ['only' => ['index', 'show']]);
        $this->middleware('can:kendaraan-create', ['only' => ['create', 'store']]);
        $this->middleware('can:kendaraan-edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:kendaraan-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $config['title'] = "Kendaraan";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Kendaraan"],
        ];
        if ($request->ajax()) {
            $data = Kendaraan::with('pemilik', 'jenis');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a class="btn btn-success" href="' . route('kendaraan.edit', $row->id) . '">Edit</a>
                        <a class="btn btn-danger btn-delete" href="#" data-id="' . $row->id . '" >Hapus</a>
                        <a class="btn btn-info" target="_blank" href="' . route('kendaraan.show', $row->id) . '"><i class="fas fa-barcode"></i></a>';
                    return $actionBtn;
                })
                ->addColumn('harga', function ($row) {
                    $text = '';
                    $harga = HargaKendaraan::select('nama', 'nominal')->where('id_kendaraan', $row->id)
                        ->join('harga', 'id_harga', '=', 'harga.id')
                        ->get();
                    foreach ($harga as $value) {
                        $text .= '<span class="badge badge-info">' . $value->nama . ' = ' . number_format($value->nominal) . '</span></br>';
                    }
                    return $text;
                })
                ->rawColumns(['harga', 'action'])
                ->make();
        }
        return view('backend.kendaraan.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function status(Request $request)
    {
        $config['title'] = "Data Ketersediaan Mobil";
        $config['breadcrumbs'] = [
            ['url' => route('kendaraan.index'), 'title' => "Data Ketersediaan Mobil"],
        ];

        if ($request->ajax()) {
            $tanggal = Carbon::now()->format('Y-m-d');
            $data = Kendaraan::select(
                'kendaraan.id',
                'kendaraan.no_kendaraan',
                'kendaraan.tahun',
                'kendaraan.warna',
                // 'kendaraan.foto',
                'jenis.nama',
                // 'jenis.harga_12',
                // 'jenis.harga_24',
                // 'range_transaksi.tanggal',
                'kendaraan.status',
            )
                ->selectRaw('(select count(id) from transaksi where id_kendaraan = kendaraan.id and transaksi.status = "proses") as s ')
                ->where('kendaraan.status', '=', 'aktif')
                ->leftJoin('jenis', 'jenis.id', '=', 'kendaraan.id_jenis')
                // ->leftJoin('range_transaksi', function ($join) use ($tanggal) {
                //     $join->on('kendaraan.id', '=', 'range_transaksi.id_kendaraan')
                //         ->where('range_transaksi.tanggal', $tanggal,);
                // })
                ->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if ($row->s !== 0) {
                        $actionBtn = '<a class="btn btn-success" href="' . route('pemesanan.createId', $row->id) . '">Pesan</a>';
                    } else {

                        $actionBtn = '<a class="btn btn-success" href="' . route('pemesanan.createId', $row->id) . '">Pesan</a>
                        <a class="btn btn-info" href="' . route('penyewaan.createId', $row->id) . '">Sewa</a>';
                    }
                    return $actionBtn;
                })
                ->make();
        }
        return view('backend.pemesanan.list_ketersediaan', compact('config'));
    }

    public function create()
    {
        $config['title'] = "Tambah Kendaraan";
        $config['breadcrumbs'] = [
            ['url' => route('kendaraan.index'), 'title' => "Kendaraan"],
            ['url' => '#', 'title' => "Tambah Kendaraan"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('kendaraan.store')
        ];
        return view('backend.kendaraan.form', compact('config'));
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
            'id_pemilik' => 'required',
            'id_jenis' => 'required',
            'no_kendaraan' => 'required',
            'tahun' => 'required',
            'warna' => 'required',
            'status' => 'required',
            'foto' => 'required|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'harga' => 'required',
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $charactersLength = strlen($characters);
                $randomString = '';
                for ($i = 0; $i < 10; $i++) {
                    $randomString .= $characters[random_int(0, $charactersLength - 1)];
                }
                $file = $request->file('foto');
                $filename = $file->getClientOriginalName();
                $file->storeAs('public/kendaraan/', $filename);
                $data = Kendaraan::create([
                    'id_pemilik' => $request['id_pemilik'],
                    'id_jenis' => $request['id_jenis'],
                    'no_kendaraan' => $request['no_kendaraan'],
                    'tahun' => $request['tahun'],
                    'warna' => $request['warna'],
                    'status' => $request['status'],
                    'foto' => $filename,
                    'barcode' => $randomString,
                ]);

                foreach ($request->harga as $idh) {
                    HargaKendaraan::create([
                        'id_kendaraan' => $data->id,
                        'id_harga' => $idh,
                    ]);
                }

                DB::commit();
                $response = response()->json($this->responseStore(true, NULL, route('kendaraan.index')));
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
        $generator = new BarcodeGeneratorPNG();

        if ($id == 0) {
            $data = Kendaraan::get();
        } else {
            $data = Kendaraan::where('id', $id)->get();
        }

        foreach ($data as $key => $d) {
            $arr[$key] = [
                'no_pol' => $d->no_kendaraan,
                'barcode' => $barcode = base64_encode($generator->getBarcode($d->barcode, $generator::TYPE_CODE_128)),
            ];
        }
        return view('backend.kendaraan.barcode', compact('arr'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $config['title'] = "Edit Kendaraan";
        $config['breadcrumbs'] = [
            ['url' => route('kendaraan.index'), 'title' => "Kendaraan"],
            ['url' => '#', 'title' => "Edit Kendaraan"],
        ];
        $data = Kendaraan::where('id', $id)->first();
        $hargaBarang = HargaKendaraan::where('id_kendaraan', $id)
            ->join('harga', 'id_harga', '=', 'harga.id')
            ->get();
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('kendaraan.update', $id)
        ];
        return view('backend.kendaraan.form', compact('config', 'data', 'hargaBarang'));
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
            'id_pemilik' => 'required',
            'id_jenis' => 'required',
            'no_kendaraan' => 'required',
            'tahun' => 'required',
            'warna' => 'required',
            'status' => 'required',
            'harga' => 'required',
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {
                $data = Kendaraan::findOrFail($id);
                if ($request->file('foto') == null) {
                    $filename = $data->foto;
                } else {
                    $file = $request->file('foto');
                    $filename = $file->getClientOriginalName();
                    $file->storeAs('public/kendaraan/', $filename);
                }
                $data->update([
                    'id_pemilik' => $request['id_pemilik'],
                    'id_jenis' => $request['id_jenis'],
                    'no_kendaraan' => $request['no_kendaraan'],
                    'tahun' => $request['tahun'],
                    'warna' => $request['warna'],
                    'status' => $request['status'],
                    'foto' => $filename,
                ]);

                $dataHarga = HargaKendaraan::where('id_kendaraan', $id)->delete();

                foreach ($request->harga as $idh) {
                    HargaKendaraan::create([
                        'id_kendaraan' => $data->id,
                        'id_harga' => $idh,
                    ]);
                }

                DB::commit();
                $response = response()->json($this->responseStore(true, NULL, route('kendaraan.index')));
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
        $data = Kendaraan::find($id);
        DB::beginTransaction();
        try {
            $data->delete();
            Storage::delete($data->foto);
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

    public function select2(Request $request)
    {
        $page = $request->page;
        $resultCount = 10;
        $offset = ($page - 1) * $resultCount;
        $data = Kendaraan::where('no_kendaraan', 'LIKE', '%' . $request->q . '%')
            ->where('status', '=', 'aktif')
            ->orderBy('no_kendaraan')
            ->skip($offset)
            ->take($resultCount)
            ->selectRaw('id, no_kendaraan as text')
            ->get();

        $count = Kendaraan::where('no_kendaraan', 'LIKE', '%' . $request->q . '%')
            ->where('status', '=', 'aktif')
            ->get()
            ->count();

        $endCount = $offset + $resultCount;
        $morePages = $count > $endCount;

        $results = array(
            "results" => $data,
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }
}
