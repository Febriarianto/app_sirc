<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pemilik;
use App\Traits\ResponseStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class PemilikController extends Controller
{
    use ResponseStatus;

    function __construct()
    {
        $this->middleware('can:pemilik-list', ['only' => ['index', 'show']]);
        $this->middleware('can:pemilik-create', ['only' => ['create', 'store']]);
        $this->middleware('can:pemilik-edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:pemilik-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $config['title'] = "Pemilik";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Pemilik"],
        ];
        if ($request->ajax()) {
            $data = Pemilik::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a class="btn btn-success" href="' . route('pemilik.edit', $row->id) . '">Edit</a>
                        <a class="btn btn-danger btn-delete" href="#" data-id="' . $row->id . '" >Hapus</a>';
                    return $actionBtn;
                })->make();
        }
        return view('backend.pemilik.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $config['title'] = "Tambah Pemilik";
        $config['breadcrumbs'] = [
            ['url' => route('pemilik.index'), 'title' => "Pemilik"],
            ['url' => '#', 'title' => "Tambah Pemilik"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('pemilik.store')
        ];
        return view('backend.pemilik.form', compact('config'));
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
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {
                $data = Pemilik::create([
                    'nama' => ucwords($request['nama']),
                    'alamat' => $request['alamat'],
                    'no_hp' => $request['no_hp'],
                ]);

                DB::commit();
                $response = response()->json($this->responseStore(true, NULL, route('pemilik.index')));
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
        $config['title'] = "Edit Pemilik";
        $config['breadcrumbs'] = [
            ['url' => route('pemilik.index'), 'title' => "Pemilik"],
            ['url' => '#', 'title' => "Edit Pemilik"],
        ];
        $data = Pemilik::where('id', $id)->first();
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('pemilik.update', $id)
        ];
        return view('backend.pemilik.form', compact('config', 'data'));
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
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {
                $data = Pemilik::findOrFail($id);

                $data->update([
                    'nama' => ucwords($request['nama']),
                    'alamat' => $request['alamat'],
                    'no_hp' => $request['no_hp'],
                ]);

                DB::commit();
                $response = response()->json($this->responseStore(true, NULL, route('pemilik.index')));
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
        $data = Pemilik::find($id);
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

    public function select2(Request $request)
    {
        $page = $request->page;
        $resultCount = 10;
        $offset = ($page - 1) * $resultCount;
        $data = Pemilik::where('nama', 'LIKE', '%' . $request->q . '%')
            ->orderBy('nama')
            ->skip($offset)
            ->take($resultCount)
            ->selectRaw('id, nama as text')
            ->get();

        $count = Pemilik::where('nama', 'LIKE', '%' . $request->q . '%')
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
