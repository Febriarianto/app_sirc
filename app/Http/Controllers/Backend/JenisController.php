<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jenis;
use App\Traits\ResponseStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class JenisController extends Controller
{
    use ResponseStatus;

    function __construct()
    {
        $this->middleware('can:jenis-list', ['only' => ['index', 'show']]);
        $this->middleware('can:jenis-create', ['only' => ['create', 'store']]);
        $this->middleware('can:jenis-edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:jenis-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $config['title'] = "Jenis";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Jenis"],
        ];
        if ($request->ajax()) {
            $data = Jenis::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a class="btn btn-success" href="' . route('jenis.edit', $row->id) . '">Edit</a>
                        <a class="btn btn-danger btn-delete" href="#" data-id="' . $row->id . '" >Hapus</a>';
                    return $actionBtn;
                })->make();
        }
        return view('backend.jenis.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $config['title'] = "Tambah Jenis";
        $config['breadcrumbs'] = [
            ['url' => route('jenis.index'), 'title' => "Jenis"],
            ['url' => '#', 'title' => "Tambah Jenis"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('jenis.store')
        ];
        return view('backend.jenis.form', compact('config'));
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
            'harga_12' => 'required',
            'harga_24' => 'required',
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {
                $data = Jenis::create([
                    'nama' => ucwords($request['nama']),
                    'harga_12' => $request['harga_12'],
                    'harga_24' => $request['harga_24'],
                ]);

                DB::commit();
                $response = response()->json($this->responseStore(true, NULL, route('jenis.index')));
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
        $config['title'] = "Edit Jenis";
        $config['breadcrumbs'] = [
            ['url' => route('jenis.index'), 'title' => "Jenis"],
            ['url' => '#', 'title' => "Edit Jenis"],
        ];
        $data = Jenis::where('id', $id)->first();
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('jenis.update', $id)
        ];
        return view('backend.jenis.form', compact('config', 'data'));
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
            'harga_12' => 'required',
            'harga_24' => 'required',
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {
                $data = Jenis::findOrFail($id);

                $data->update([
                    'nama' => ucwords($request['nama']),
                    'harga_12' => $request['harga_12'],
                    'harga_24' => $request['harga_24'],
                ]);

                DB::commit();
                $response = response()->json($this->responseStore(true, NULL, route('jenis.index')));
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
        $data = Jenis::find($id);
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
        $data = Jenis::where('nama', 'LIKE', '%' . $request->q . '%')
            ->orderBy('nama')
            ->skip($offset)
            ->take($resultCount)
            ->selectRaw('id, nama as text')
            ->get();

        $count = Jenis::where('nama', 'LIKE', '%' . $request->q . '%')
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
