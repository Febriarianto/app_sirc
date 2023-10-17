<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penyewa;
use App\Traits\ResponseStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class PenyewaController extends Controller
{
    use ResponseStatus;

    function __construct()
    {
        $this->middleware('can:penyewa-list', ['only' => ['index', 'show']]);
        $this->middleware('can:penyewa-create', ['only' => ['create', 'store']]);
        $this->middleware('can:penyewa-edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:penyewa-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $config['title'] = "Penyewa";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Penyewa"],
        ];
        if ($request->ajax()) {
            $data = Penyewa::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a class="btn btn-success" href="' . route('penyewa.edit', $row->id) . '">Edit</a>
                        <a class="btn btn-danger btn-delete" href="#" data-id="' . $row->id . '" >Hapus</a>';
                    return $actionBtn;
                })->make();
        }
        return view('backend.penyewa.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $config['title'] = "Tambah Penyewa";
        $config['breadcrumbs'] = [
            ['url' => route('penyewa.index'), 'title' => "Penyewa"],
            ['url' => '#', 'title' => "Tambah Penyewa"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('penyewa.store')
        ];
        return view('backend.penyewa.form', compact('config'));
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
            'nik' => 'required|unique:penyewa',
            'nama' => 'required',
            'no_hp' => 'required',
            'alamat' => 'required',
            'ktp' => 'required|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'kk' => 'required|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {
                $imgKtp = $request->file('ktp')->store('ktp', 'public');
                $imgKk = $request->file('kk')->store('kk', 'public');
                $data = Penyewa::create([
                    'nik' => $request['nik'],
                    'nama' => $request['nama'],
                    'no_hp' => $request['no_hp'],
                    'alamat' => $request['alamat'],
                    'ktp' => $imgKtp,
                    'kk' => $imgKk,
                ]);

                DB::commit();
                $response = response()->json($this->responseStore(true, NULL, route('penyewa.index')));
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
        $config['title'] = "Edit Penyewa";
        $config['breadcrumbs'] = [
            ['url' => route('penyewa.index'), 'title' => "Penyewa"],
            ['url' => '#', 'title' => "Edit Penyewa"],
        ];
        $data = Penyewa::where('id', $id)->first();
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('penyewa.update', $id)
        ];
        return view('backend.penyewa.form', compact('config', 'data'));
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
        // dd($request);
        $validator = Validator::make($request->all(), [
            'nama' => 'required|nik|unique:penyewa,nik,' . $request['nik'] . ',nik',
            'nama' => 'required',
            'no_hp' => 'required',
            'alamat' => 'required',
            // 'ktp' => 'required|mimes:jpg,png,jpeg,gif,svg|max:2048',
            // 'kk' => 'required|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {
                $data = Penyewa::findOrFail($id);
                if (!empty($request->file('ktp'))) {
                    $imgKtp = $request->file('ktp')->store('ktp', 'public');
                } else {
                    $imgKtp = $data->ktp;
                }

                if (!empty($request->file('kk'))) {
                    $imgKk = $request->file('kk')->store('kk', 'public');
                } else {
                    $imgKk = $data->kk;
                }

                $data->update([
                    'nik' => $request['nik'],
                    'nama' => $request['nama'],
                    'no_hp' => $request['no_hp'],
                    'alamat' => $request['alamat'],
                    'ktp' => $imgKtp,
                    'kk' => $imgKk,
                ]);

                DB::commit();
                $response = response()->json($this->responseStore(true, NULL, route('penyewa.index')));
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
        $data = Penyewa::find($id);
        DB::beginTransaction();
        try {
            $data->delete();
            \Storage::delete($data->ktp);
            \Storage::delete($data->kk);
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
        $data = Penyewa::where('nik', 'LIKE', '%' . $request->q . '%')
            ->orderBy('nik')
            ->skip($offset)
            ->take($resultCount)
            ->selectRaw('id, nik as text')
            ->get();

        $count = Penyewa::where('nik', 'LIKE', '%' . $request->q . '%')
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
