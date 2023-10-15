<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

trait ValidateLogin
{

  protected function sendFailedLoginResponse(Request $request)
  {
    throw ValidationException::withMessages([
      'login' => [trans('auth.failed')],
    ]);
  }

  protected function guard()
  {
    return Auth::guard();
  }

  protected function validateLogin(Request $request)
  {
    $request->validate([
      'email' => 'required|string',
      'password' => 'required|string',
    ]);
  }
}
