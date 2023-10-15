<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Traits\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
  use ThrottlesLogins;

  public function __construct()
  {
    $this->middleware('guest:web')->except('logout');
  }

  public function showLoginForm()
  {
    return view('auth.login');
  }

  public function login(Request $request)
  {
    $this->validateLogin($request);
    if (
      method_exists($this, 'hasTooManyLoginAttempts') &&
      $this->hasTooManyLoginAttempts($request)
    ) {
      $this->fireLockoutEvent($request);
      $this->sendLockoutResponse($request);
    }

    $remember = $request->has('remember');

    $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
    $data = [
      $fieldType => $request['email'],
      'password' => $request['password']
    ];

    if (Auth::attempt($data, $remember)) {
      return redirect(Auth::user()->roles->dashboard_url);
    }

    $this->incrementLoginAttempts($request);
    $this->sendFailedLoginResponse($request);
    return redirect(RouteServiceProvider::LOGIN);
  }

  public function logout(Request $request)
  {
    $redirect = redirect(RouteServiceProvider::LOGIN);
    $this->guard()->logout();
    $request->session()->invalidate();
    $request->session()->regenerate();
    return $redirect;
  }

  public function redirectPath()
  {
    if (method_exists($this, 'redirectTo')) {
      return $this->redirectTo();
    }

    return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
  }

  protected function sendFailedLoginResponse(Request $request)
  {
    throw ValidationException::withMessages([
      'email' => [trans('auth.failed')],
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
