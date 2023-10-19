<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Referral;
use App\Traits\ResponseStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class ReferralController extends Controller
{
    use ResponseStatus;

    function __construct()
    {
        $this->middleware('can:referral-list', ['only' => ['index', 'show']]);
        $this->middleware('can:referral-create', ['only' => ['create', 'store']]);
        $this->middleware('can:referral-edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:referral-delete', ['only' => ['destroy']]);
    }


    public function index(Request $request)
    {
        $config['title'] = "Referral";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Referral"],
        ];
        if ($request->ajax()) {
            $data = Referral::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a class="btn btn-success" href="' . route('referral.edit', $row->id) . '">Edit</a>
                        <a class="btn btn-danger btn-delete" href="#" data-id="' . $row->id . '" >Hapus</a>';
                    return $actionBtn;
                })->make();
        }
        return view('backend.referral.index', compact('config'));
    }

    public function create()
    {
        $config['title'] = "Tambah Referral";
        $config['breadcrumbs'] = [
            ['url' => route('referral.index'), 'title' => "Referral"],
            ['url' => '#', 'title' => "Tambah Referral"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('referral.store')
        ];
        return view('backend.referral.form', compact('config'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
            'no_rekening' => 'nullable',
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {
                $data = Referral::create([
                    'nama' => ucwords($request['nama']),
                    'alamat' => $request['alamat'],
                    'no_hp' => $request['no_hp'],
                    'no_rekening' => $request['no_rekening'],
                ]);

                DB::commit();
                $response = response()->json($this->responseStore(true, NULL, route('referral.index')));
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

    public function edit($id)
    {
        $config['title'] = "Edit Referral";
        $config['breadcrumbs'] = [
            ['url' => route('referral.index'), 'title' => "Referral"],
            ['url' => '#', 'title' => "Edit Referral"],
        ];
        $data = Referral::where('id', $id)->first();
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('referral.update', $id)
        ];
        return view('backend.referral.form', compact('config', 'data'));
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
            'no_rekening' => 'nullable',
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {
                $data = Referral::findOrFail($id);

                $data->update([
                    'nama' => ucwords($request['nama']),
                    'alamat' => $request['alamat'],
                    'no_hp' => $request['no_hp'],
                    'no_rekening' => $request['no_rekening'],
                ]);

                DB::commit();
                $response = response()->json($this->responseStore(true, NULL, route('referral.index')));
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
        $data = Referral::find($id);
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
        $data = Referral::where('nama', 'LIKE', '%' . $request->q . '%')
            ->orderBy('nama')
            ->skip($offset)
            ->take($resultCount)
            ->selectRaw('id, nama as text')
            ->get();

        $count = Referral::where('nama', 'LIKE', '%' . $request->q . '%')
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
