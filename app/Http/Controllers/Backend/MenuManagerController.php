<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Permissions\MenuManager;
use App\Models\Permissions\Permissions;
use App\Traits\ResponseStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MenuManagerController extends Controller
{
  use ResponseStatus;

  function __construct()
  {
    $this->middleware('role:super-admin', ['only' => ['index', 'show']]);
  }

  public function index()
  {
    $config['title'] = "Menu Manager";
    $config['breadcrumbs'] = [
      ['url' => '#', 'title' => "Menu Manager"],
    ];
    $config['form'] = (object)[
      'method' => 'POST',
      'action' => route('menu-manager.store')
    ];
    $sortable = self::getMenu();
    return view('backend.menumanager.index', compact('config', 'sortable'));
  }

  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'type' => 'required|in:module,header,line,static',
    ]);
    if ($validator->passes()) {
      DB::beginTransaction();
      try {
        $menuManager = MenuManager::create([
          'title' => ucwords($request['title']),
          'slug' => $request['slug'],
          'path_url' => $request['path_url'],
          'icon' => $request['icon'],
          'type' => $request['type'],
          'sort' => MenuManager::where([
            ['parent_id', $request['parent_id'] ?? 0]
          ])->max('sort') + 1
        ]);
        if ($menuManager->type == 'module') :
          $defaultPermission = ['List', 'Create', 'Edit', 'Delete'];
          foreach ($defaultPermission as $item) :
            Permissions::create([
              'menu_manager_id' => $menuManager->id,
              'slug' => $request['slug'] . " " . $item,
              'name' => ($request['title']) . " " . $item
            ]);
          endforeach;
        endif;

        DB::commit();
        $response = response()->json($this->responseStore(true, NULL, route('menu-manager.index')));
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

  public function edit($id, Request $request)
  {
    $config['title'] = "Menu Manager";
    $config['breadcrumbs'] = [
      ['url' => '#', 'title' => "Menu Manager"],
    ];
    $config['form'] = (object)[
      'method' => 'PUT',
      'action' => route('menu-manager.update', $id)
    ];
    $data = MenuManager::find($id);
    $sortable = self::getMenu();
    return view('backend.menumanager.index', compact('config', 'sortable', 'data'));
  }

  public function update(Request $request, $id)
  {
    $validator = Validator::make($request->all(), [
      'type' => 'required|in:module,header,line,static'
    ]);

    if ($validator->passes()) {
      $menuManager = MenuManager::find($id);
      $permission = Permissions::where('menu_manager_id', $id)->orderBy('id', 'asc');
      DB::beginTransaction();
      try {
        if ($request['type'] == 'module') {
          $menuManager->update([
            'title' => $request['title'],
            'path_url' => $request['path_url'],
            'type' => $request['type'],
            'icon' => $request['icon'],
            'slug' => $request['slug']
          ]);
          $defaultPermission = ['List', 'Create', 'Edit', 'Delete'];
          if ($permission->count() > 0) {
            foreach ($permission->get() as $key => $item) :
              $item->update([
                'id' => $item->id,
                'menu_manager_id' => $menuManager->id,
                'slug' => $request['slug'] . " " . $defaultPermission[$key],
                'name' => ($request['title']) . " " . $defaultPermission[$key]
              ]);
            endforeach;
          } else {
            foreach ($defaultPermission as $key => $item) :
              Permissions::create([
                'menu_manager_id' => $menuManager->id,
                'slug' => $request['slug'] . " " . $item,
                'name' => ($request['title']) . " " . $item
              ]);
            endforeach;
          }
        } elseif ($request['type'] == 'header' || $request['type'] == 'static') {
          if ($menuManager->type == 'module' && $permission->count() > 0) {
            $permission->delete();
          }
          $menuManager->update([
            'title' => $request['title'],
            'path_url' => NULL,
            'type' => $request['type'],
            'icon' => $request['icon'],
            'slug' => NULL
          ]);
        } elseif ($request['type'] == 'line') {
          if ($menuManager->type == 'module' && $permission->count() > 0) {
            $permission->delete();
          }
          $menuManager->update([
            'title' => NULL,
            'path_url' => NULL,
            'type' => $request['type'],
            'icon' => NULL,
            'slug' => NULL
          ]);
        }

        DB::commit();
        $response = response()->json($this->responseStore(true, NULL, route('menu-manager.index')));
      } catch (\Throwable $throw) {
        Log::error($throw);
        DB::rollback();
        $response = response()->json([
          'status' => 'error',
          'message' => $throw->getMessage()
        ]);
      }
    } else {
      $response = response()->json(['error' => $validator->errors()->all()]);
    }
    return $response;
  }

  public function destroy($id)
  {
    $response = response()->json([
      'status' => 'error',
      'message' => 'Data gagal dihapus'
    ]);
    DB::beginTransaction();

    try {
      $data = MenuManager::findOrFail($id);
      $child = MenuManager::where('parent_id', $id)->get();
      $data->delete();
      foreach ($child as $item) :
        MenuManager::find($item['id'])->update([
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
    $menuManager = new MenuManager;
    $roots = $menuManager->getall();
    return self::tree($roots);
  }

  private static function tree($roots)
  {
    $html = '<ol class="dd-list"> ';
    foreach ($roots as $item) {
      $find = MenuManager::where('parent_id', $item['id'])->orderBy('sort', 'asc');
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
      $html .= '<a href="' . route('menu-manager.edit', $item->id) . '" class="btn btn-sm btn-default"><i class="fa fa-fw fa-edit"></i></a>
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
        MenuManager::find($item['id'])->update([
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
