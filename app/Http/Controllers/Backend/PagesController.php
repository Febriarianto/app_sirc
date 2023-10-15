<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pages;
use App\Traits\ResponseStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PagesController extends Controller
{
    use ResponseStatus;

    function __construct()
    {
        $this->middleware('can:pages-list', ['only' => ['index', 'show']]);
        $this->middleware('can:pages-create', ['only' => ['create', 'store']]);
        $this->middleware('can:pages-edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:pages-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $config['title'] = "Pages";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Pages"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('pages.store')
        ];
        $sortable = self::getMenu();
        return view('backend.pages.index', compact('config', 'sortable'));
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
        // dd($request);
        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {
                $pages = Pages::create([
                    'title' => ucwords($request['title']),
                    'slug' => $request['slug'],
                    'sort' => Pages::where([
                        ['parent_id', $request['parent_id'] ?? 0]
                    ])->max('sort') + 1
                ]);

                DB::commit();
                $response = response()->json($this->responseStore(true, NULL, route('pages.index')));
            } catch (\Throwable $throw) {
                Log::error($throw);
                DB::rollBack();
                $response = $throw;
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
    public function edit($id, Request $request)
    {
        $config['title'] = "Pages";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Pages"],
        ];
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('pages.update', $id)
        ];
        $data = Pages::find($id);
        $sortable = self::getMenu();
        return view('backend.pages.index', compact('config', 'sortable', 'data'));
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
        $response = response()->json([
            'status' => 'error',
            'message' => 'Data gagal dihapus'
        ]);
        DB::beginTransaction();

        try {
            $data = Pages::findOrFail($id);
            $child = Pages::where('parent_id', $id)->get();
            $data->delete();
            foreach ($child as $item) :
                Pages::find($item['id'])->update([
                    'parent_id' => '0'
                ]);
            endforeach;
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

    public static function getMenu()
    {
        $pages = new Pages;
        $roots = $pages->getall();
        return self::tree($roots);
    }

    private static function tree($roots)
    {
        $html = '<ol class="dd-list"> ';
        foreach ($roots as $item) {
            $find = Pages::where('parent_id', $item['id'])->orderBy('sort', 'asc');
            $html .= '
                       <li class="dd-item dd3-item" data-id="' . $item->id . '">
                         <div class="dd-handle dd3-handle"></div>
                         <div class="dd3-content">' . ($item->type == 'line' ? 'Line' : $item->title) . '</div>
                         <div class="dd3-actions">
                           <div class="btn-group">';
            if ($item->type == 'module') {
                $html .= '<a href="#" class="btn btn-sm font-size-14">M</a>';
            } else if ($item->type == 'header') {
                $html .= '<a href="#" class="btn btn-sm font-size-14">H</a>';
            } else if ($item->type == 'line') {
                $html .= '<a href="#" class="btn btn-sm font-size-14">L</a>';
            } else if ($item->type == 'static') {
                $html .= '<a href="#" class="btn btn-sm font-size-14">S</a>';
            }
            $html .= '<a href="' . route('pages.edit', $item->id) . '" class="btn btn-sm btn-default"><i class="fa fa-fw fa-edit"></i></a>
                          <button
                            type="button"
                            class="btn btn-sm btn-delete btn-default"
                            data-id="' . $item->id . '"
                            ><i class="fa fa-fw fa-trash"></i>
                          </button>
                        </div>
                      </div>
                    ';

            if ($find->count()) {
                $html .= self::tree($find->get());
            }
            $html .= '</li>';
        }
        $html .= '</ol>';
        return $html;
    }

    public function changeHierarchy(Request $request)
    {
        $data = json_decode($request['hierarchy'], TRUE);
        $menuItems = $this->render_menu_hierarchy($data);

        DB::beginTransaction();
        try {
            foreach ($menuItems as $item) :
                Pages::find($item['id'])->update([
                    'parent_id' => $item['parent_id'],
                    'sort' => $item['sort']
                ]);
            endforeach;
            DB::commit();
            $response = response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dihapus',
                'redirect' => "reload"
            ]);
        } catch (\Throwable $throw) {
            DB::rollback();
            $response = response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus data'
            ]);
        }
        return $response;
    }

    public function render_menu_hierarchy($data = array(), $parentMenu = 0, $result = array())
    {
        foreach ($data as $key => $val) {
            $row['id'] = $val['id'];
            $row['parent_id'] = $parentMenu;
            $row['sort'] = ($key + 1);
            array_push($result, $row);
            if (isset($val['children']) && $val['children'] > 0) {
                $result = array_merge($result, $this->render_menu_hierarchy($val['children'], $val['id']));
            }
        }
        return $result;
    }
}
