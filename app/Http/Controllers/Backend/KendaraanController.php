<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kendaraan;
use App\Traits\ResponseStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;

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
                        <a class="btn btn-danger btn-delete" href="#" data-id="' . $row->id . '" >Hapus</a>';
                    return $actionBtn;
                })->make();
        }
        return view('backend.kendaraan.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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
            'foto' => 'required|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {
                $img = $request->file('foto')->store('kendaraan', 'public');
                $data = Kendaraan::create([
                    'id_pemilik' => $request['id_pemilik'],
                    'id_jenis' => $request['id_jenis'],
                    'no_kendaraan' => $request['no_kendaraan'],
                    'tahun' => $request['tahun'],
                    'warna' => $request['warna'],
                    'foto' => $img,
                ]);

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
        $config['title'] = "Edit Kendaraan";
        $config['breadcrumbs'] = [
            ['url' => route('kendaraan.index'), 'title' => "Kendaraan"],
            ['url' => '#', 'title' => "Edit Kendaraan"],
        ];
        $data = Kendaraan::where('id', $id)->first();
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('kendaraan.update', $id)
        ];
        return view('backend.kendaraan.form', compact('config', 'data'));
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
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {
                $data = Kendaraan::findOrFail($id);
                if ($request->file('foto') == null) {
                    $img = $data->foto;
                } else {
                    $img = $request->file('foto')->store('kendaraan', 'public');
                }
                $data->update([
                    'id_pemilik' => $request['id_pemilik'],
                    'id_jenis' => $request['id_jenis'],
                    'no_kendaraan' => $request['no_kendaraan'],
                    'tahun' => $request['tahun'],
                    'warna' => $request['warna'],
                    'foto' => $img,
                ]);

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
            \Storage::delete($data->foto);
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