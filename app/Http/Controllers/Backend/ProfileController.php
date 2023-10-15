<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\FileUpload;
use App\Http\Controllers\Controller;
use App\Models\Puskesmas;
use App\Models\User;
use App\Traits\ResponseStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class ProfileController extends Controller
{
  use ResponseStatus;

  public function index(Request $request)
  {
    $config['title'] = "Ubah Profile";
    $config['breadcrumbs'] = [
      ['url' => '#', 'title' => "Ubah Profile"],
    ];

    $data = User::with('roles')->find(auth()->id());

    $config['form'] = (object)[
      'method' => 'POST',
      'action' => route('profile.store')
    ];
    return view('backend.users.profile', compact('config', 'data'));
  }

  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'poster' => 'image|mimes:jpg,png,jpeg|max:5000',
      'name' => 'required',
      'username' => 'required|alpha_dash|unique:users,username,' . $request['username'] . ',username',
      'email' => 'required|email|unique:users,email,' . $request['email'] . ',email',
    ]);
    if ($validator->passes()) {
      DB::beginTransaction();
      $image = NULL;
      $dimensions = [array('300', '300', 'thumbnail')];
      try {
        $data = User::findOrFail(auth()->id());

        if (isset($request['image']) && !empty($request['image'])) {
          $image = FileUpload::uploadImage('image', $dimensions, 'storage', $data['image']);
        }
        $data->update([
          'name' => ucwords($request['name']),
          'email' => $request['email'],
          'username' => $request['username'],
          'image' => $image,
        ]);

        DB::commit();
        $response = response()->json($this->responseStore(true, NULL, route('dashboard')));
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
}
