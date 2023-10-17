<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Traits\ResponseStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class FakturController extends Controller
{
    use ResponseStatus;

    function __construct()
    {
        $this->middleware('can:faktur-list', ['only' => ['index', 'show']]);
        $this->middleware('can:faktur-create', ['only' => ['create', 'store']]);
        $this->middleware('can:faktur-edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:faktur-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $config['title'] = "Faktur";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Faktur"],
        ];
        if ($request->ajax()) {
            $data = Transaksi::with('penyewa')->where('tipe', '=', 'faktur')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a class="btn btn-success" href="' . route('faktur.edit', $row->id) . '">Edit</a>
                        <a class="btn btn-danger btn-delete" href="#" data-id="' . $row->id . '" >Hapus</a>';
                    return $actionBtn;
                })->make();
        }
        return view('backend.faktur.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $config['title'] = "Tambah Faktur";
        $config['breadcrumbs'] = [
            ['url' => route('faktur.index'), 'title' => "Faktur"],
            ['url' => '#', 'title' => "Tambah Faktur"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('faktur.store')
        ];
        return view('backend.faktur.form', compact('config'));
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
        //
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
        //
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
