<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ResponseStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Helpers\FileUpload;
use App\Models\Setting;

class SettingController extends Controller
{
    use ResponseStatus;

    function __construct()
    {
        $this->middleware('can:setting-list', ['only' => ['index', 'show']]);
        $this->middleware('can:setting-create', ['only' => ['create', 'store']]);
        $this->middleware('can:setting-edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:setting-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $config['title'] = "Setting";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Setting"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('setting.store')
        ];
        $data = Setting::first();
        return view('backend.setting.index', compact('data', 'config'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        $config['title'] = "Update Setting";
        $config['breadcrumbs'] = [
            ['url' => route('setting.index'), 'title' => "Setting"],
            ['url' => '#', 'title' => "Update Setting"],
        ];
        $data = Setting::first();
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('setting.update', $id)
        ];
        return view('backend.setting.form', compact('config', 'data'));
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

        DB::beginTransaction();
        $logo = NULL;
        $favicon = NULL;
        $dimensions = [array('300', '300', 'setting')];
        try {

            $data = Setting::findOrFail($id);
            if (isset($request['logo']) && !empty($request['logo'])) {
                $logo = FileUpload::uploadImage('logo', $dimensions, 'storage', $data['logo']);
            }

            if (isset($request['favicon']) && !empty($request['favicon'])) {
                $favicon = FileUpload::uploadImage('favicon', $dimensions, 'storage', $data['favicon']);
            }

            $data->update([
                'logo' => $logo ?? $data->logo,
                'favicon' => $favicon ?? $data->favicon,
                'title' => $request['title'],
                'deskripsi' => $request['deskripsi'],
                'alamat' => $request['alamat'],
                'maps' => $request['maps'],
                'telp' => $request['telp'],
                'fax' => $request['fax'],
                'email' => $request['email'],
                'facebook' => $request['facebook'],
                'instagram' => $request['instagram'],
                'youtube' => $request['youtube'],
            ]);

            DB::commit();
            $response = response()->json($this->responseStore(true, NULL, route('setting.index')));
        } catch (\Throwable $throw) {
            DB::rollBack();
            Log::error($throw);
            $response = response()->json(['error' => $throw->getMessage()]);
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
