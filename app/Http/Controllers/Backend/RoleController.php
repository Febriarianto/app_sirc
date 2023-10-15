<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Permissions\MenuManager;
use App\Models\Permissions\MenuManagerRole;
use App\Models\Permissions\PermissionRole;
use App\Models\Permissions\Permissions;
use App\Models\Role;
use App\Traits\ResponseStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class RoleController extends Controller
{
  use ResponseStatus;

  function __construct()
  {
    $this->middleware('can:role-list', ['only' => ['index', 'show']]);
    $this->middleware('can:role-create', ['only' => ['create', 'store']]);
    $this->middleware('can:role-edit', ['only' => ['edit', 'update']]);
    $this->middleware('can:role-delete', ['only' => ['destroy']]);
  }

  public function index(Request $request)
  {
    $config['title'] = "Role";
    $config['breadcrumbs'] = [
      ['url' => '#', 'title' => "Role"],
    ];
    if ($request->ajax()) {
      $data = Role::query();
      return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('action', function ($row) {
          $actionBtn = '<a class="btn btn-success" href="' . route('roles.edit', $row->id) . '">Edit</a>
                        <a class="btn btn-danger btn-delete" href="#" data-id="' . $row->id . '" >Hapus</a>';
          return $actionBtn;
        })->make();
    }
    return view('backend.roles.index', compact('config'));
  }

  public function create()
  {
    $config['title'] = "Tambah Role";
    $config['breadcrumbs'] = [
      ['url' => route('roles.index'), 'title' => "Role"],
      ['url' => '#', 'title' => "Tambah Role"],
    ];
    $config['form'] = (object)[
      'method' => 'POST',
      'action' => route('roles.store')
    ];
    $permissions = self::getStaticMenu();
    return view('backend.roles.form', compact('config', 'permissions'));
  }

  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required|unique:roles'
    ]);
    if ($validator->passes()) {
      DB::beginTransaction();
      try {
        /* Save Role */
        $role = Role::create([
          'name' => $request['name'],
          'dashboard_url' => '/backend/dashboard'
        ]);

        /* Save Menu Manager For Role */
        $data_menus = array_map(function ($item) use ($role) {
          return [
            'role_id' => $role->id,
            'menu_manager_id' => $item['menu_manager_id']
          ];
        }, (isset($request['menus']) ? $request['menus'] : []));

        MenuManagerRole::insert($data_menus);

        /* Save Permissions For Role */
        $data_permissions = array_map(function ($item) use ($role) {
          return [
            'role_id' => $role->id,
            'permission_id' => $item['permission_id']
          ];
        }, (isset($request['permissions']) ? $request['permissions'] : []));

        PermissionRole::insert($data_permissions);

        DB::commit();
        $response = response()->json($this->responseStore(true, NULL, route('roles.index')));
      } catch (\Throwable $throw) {
        Log::error($throw);
        DB::rollBack();
        $response = response()->json(['error' => $throw->getMessage()]);
      }
    } else {
      $response = response()->json(['error' => $validator->errors()->all()]);
    }
    return $response;
  }

  public function edit($id)
  {
    $config['title'] = "Edit Role";
    $config['breadcrumbs'] = [
      ['url' => route('roles.index'), 'title' => "Role"],
      ['url' => '#', 'title' => "Edit Role"],
    ];
    $permissions = self::getStaticMenu($id);
    $data = Role::find($id);
    $config['form'] = (object)[
      'method' => 'PUT',
      'action' => route('roles.update', $id)
    ];
    return view('backend.roles.form', compact('config', 'data', 'permissions'));
  }

  public function update(Request $request, $id)
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required|unique:roles,name,' . $request['name'] . ',name',
    ]);
    if ($validator->passes()) {
      DB::beginTransaction();
      try {
        $role = Role::findorfail($id);
        $role->name = $request['name'];
        $role->save();

        $role_menu = MenuManagerRole::where('role_id', $role->id)->delete();
        $role_permissions = PermissionRole::where('role_id', $role->id)->delete();

        /* Save Menu Manager For Role */
        $data_menus = array_map(function ($item) use ($role) {
          return [
            'role_id' => $role->id,
            'menu_manager_id' => $item['menu_manager_id']
          ];
        }, (isset($request['menus']) ? $request['menus'] : []));

        MenuManagerRole::insert($data_menus);

        /* Save Permissions For Role */
        $data_permissions = array_map(function ($item) use ($role) {
          return [
            'role_id' => $role->id,
            'permission_id' => $item['permission_id']
          ];
        }, (isset($request['permissions']) ? $request['permissions'] : []));

        PermissionRole::insert($data_permissions);

        DB::commit();
        $response = response()->json($this->responseStore(true, NULL, route('roles.index')));
      } catch (\Throwable $throw) {
        Log::error($throw);
        DB::rollBack();
        $response = response()->json(['error' => $throw->getMessage()]);
      }
    } else {
      $response = response()->json(['error' => $validator->errors()->all()]);
    }
    return $response;
  }

  public function destroy(Role $role)
  {
    $response = response()->json([
      'status' => 'error',
      'message' => 'Data gagal dihapus'
    ]);
    DB::beginTransaction();
    try {
      $role->delete();
      $response = response()->json([
        'status' => 'success',
        'message' => 'Data berhasil dihapus'
      ]);
      DB::commit();
    } catch (\Throwable $throw) {
      Log::error($throw);
      DB::rollBack();
      $response = response()->json(['error' => $throw]);
    }

    return $response;
  }

  public static function getStaticMenu($role = NULL)
  {
    $menuManager = new MenuManager;
    $roots = $menuManager->getall();
    //    $roots = $menu_list->whereIn('type', ['module', 'header', 'line']);
    return self::tree($roots, $role);
  }

  private static function tree($roots, $role = NULL)
  {
    $html = '
       <table class="table table-sm table-striped text-nowrap">
          <thead>
          <tr>
             <th>List Hak Akses</th>
             <th>
                <div class="form-check form-check-inline">
                   <input class="form-check-input feature-all" type="checkbox">
                </div>
             </th>
             <th>Hak Akses</th>
          </tr>
          </thead>
          <tbody>';
    $html .= self::recrusive($roots, $role);
    $html .= '
              </tbody>
           </table>
         ';
    return $html;
  }

  public static function recrusive($roots = [], $role = NULL)
  {
    $html = '';
    foreach ($roots ?? [] as $item) :
      $child = MenuManager::where('parent_id', $item['id'])->orderBy('sort', 'asc');
      $html .= self::itemtype($item, $role);
      if ($child->count() > 0) {
        $html .= self::recrusive($child->get(), $role);
      }
    endforeach;
    return $html;
  }

  public static function itemtype($item, $role)
  {
    if (!empty($role)) :
      $role_permission = Role::find($role)->permissions_role()->get()->pluck('permission_id')->toArray();
      $role_menu = Role::find($role)->menu_manager()->get()->pluck('menu_manager_id')->toArray();
    endif;

    $html = '';
    if ($item->type == 'module') :
      $permissions = Permissions::where('menu_manager_id', $item->id)->get();
    elseif ($item->type == 'static') :
      return '';
    endif;

    $itemName = match ($item->type) {
      'module', 'static' => $item->title,
      'header' => "{$item->title} (Header)",
      'line' => 'Divider',
    };
    $html .= '
      <tr>
          <td>
              <div class="form-check form-check-inline">
                  <input class="form-check-input menu-item" type="checkbox" name="menus[][menu_manager_id]" value="' . $item->id . '" id="' . $item->id . $item->title . '"
                   onclick="' . ($item->type == 'module' ? "return false;" : NULL) . '"' . (isset($role_menu) && in_array($item->id, $role_menu) ? 'checked' : '') . '>
                  <label class="form-check-label" for="' . $item->id . $item->title . '">' . $itemName . '</label>
          </div>
          </td>';
    if ($item->type == 'module') :
      $html .= '
            <td>
              <div class="form-check form-check-inline">
                  <input class="form-check-input function-all" type="checkbox">
                  <label class="form-check-label" for="">Semua</label>
              </div>
            </td>';
    else :
      $html .= '<td></td>';
    endif;

    $html .= '<td>
          <div class="row">
       ';
    if ($item->type == 'module') :
      foreach ($permissions ?? array() as $permission) {
        $html .= '
              <div class="col-sm-6">
              <div class="form-check form-check-inline">
                  <input class="form-check-input function-item" type="checkbox" name="permissions[][permission_id]" value="' . $permission->id . '"
                  id="' . $permission->id . '" ' . (isset($role_permission) && in_array($permission->id, $role_permission) ? 'checked' : '') . '>
                  <label class="form-check-label" for="' . $permission->id . '">' . $permission->name . '</label>
              </div>
              </div>
            ';
      }
    endif;

    $html .= '
          </div>
          </td>
          </tr>
         ';
    return $html;
  }

  public function select2(Request $request)
  {
    $page = $request->page;
    $resultCount = 10;
    $offset = ($page - 1) * $resultCount;
    $data = Role::where('name', 'LIKE', '%' . $request->q . '%')
      ->orderBy('name')
      ->skip($offset)
      ->take($resultCount)
      ->selectRaw('id, name as text')
      ->get();

    $count = Role::where('name', 'LIKE', '%' . $request->q . '%')
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
