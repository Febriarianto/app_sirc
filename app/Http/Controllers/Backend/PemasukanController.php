<?php

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use App\Traits\ResponseStatus;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use DateTime;

class PemasukanController extends Controller
{
    use ResponseStatus;

    function __construct()
    {
        $this->middleware('can:pemasukan-list', ['only' => ['index', 'show']]);
        $this->middleware('can:pemasukan-create', ['only' => ['create', 'store']]);
        $this->middleware('can:pemasukan-edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:pemasukan-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $config['title'] = "Pemasukan";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Pemasukan"],
        ];
        if ($request->ajax()) {
            $data = Pembayaran::where('status', 'pemasukan')->where('tipe', 'lainnya')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if ($row->created_at->format("Y-m-d") !== Carbon::today()->format("Y-m-d")) {
                        $actionBtn = "";
                    } else {
                        $actionBtn = '<a class="btn btn-success" href="' . route('pemasukan.edit', $row->id) . '">Edit</a>
                            <a class="btn btn-danger btn-delete" href="#" data-id="' . $row->id . '" >Hapus</a>';
                    }
                    return $actionBtn;
                })->make();
        }
        return view('backend.pemasukan.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $config['title'] = "Tambah Pemasukan";
        $config['breadcrumbs'] = [
            ['url' => route('pemasukan.index'), 'title' => "Pemasukan"],
            ['url' => '#', 'title' => "Tambah Pemasukan"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('pemasukan.store')
        ];
        return view('backend.pemasukan.form', compact('config'));
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
            'nominal' => 'required',
            'detail' => 'required',
            'metode' => 'required',
            'file' => isset($request['file']) ? 'required|mimes:jpg,png,jpeg,gif,svg|max:2048' : '',
        ]);

        if ($validator->passes()) {
            DB::beginTransaction();
            try {

                if (isset($request['file'])) {
                    $imgTrf = date("Y-m-d") . '_' . $request->file->getClientOriginalName();
                    $request->file->storeAs('public/buktiTrf/', $imgTrf);
                } else {
                    $imgTrf = '';
                }

                $data = Pembayaran::create([
                    'status' => 'pemasukan',
                    'nominal' => $request['nominal'],
                    'detail' => $request['detail'],
                    'metode' => $request['metode'],
                    'file' => $imgTrf,
                    'penerima' => Auth()->user()->name,
                    'tipe' => 'lainnya',
                ]);

                DB::commit();
                $response = response()->json($this->responseStore(true, NULL, route('pemasukan.index')));
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
        $config['title'] = "Edit Pemasukan";
        $config['breadcrumbs'] = [
            ['url' => route('pemasukan.index'), 'title' => "Pemasukan"],
            ['url' => '#', 'title' => "Edit Pemasukan"],
        ];
        $data = Pembayaran::where('id', $id)->first();
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('pemasukan.update', $id)
        ];
        return view('backend.pemasukan.form', compact('config', 'data'));
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
            // 'status' => 'required',
            'nominal' => 'required',
            'detail' => 'required',
            'metode' => 'required',
            'file' => isset($request['file']) ? 'required|mimes:jpg,png,jpeg,gif,svg|max:2048' : '',
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {

                $data = Pembayaran::findOrFail($id);

                if ($request->file('file') == null) {
                    $imgTrf = $data->foto;
                } else {
                    $imgTrf = date("Y-m-d") . '_' . $request->file->getClientOriginalName();
                    $request->file->storeAs('public/buktiTrf/', $imgTrf);
                }

                $data->update([
                    'nominal' => $request['nominal'],
                    'detail' => $request['detail'],
                    'metode' => $request['metode'],
                    'file' => $imgTrf,
                ]);

                DB::commit();
                $response = response()->json($this->responseStore(true, NULL, route('pemasukan.index')));
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
        $data = Pembayaran::find($id);
        DB::beginTransaction();
        try {
            $data->delete();
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
