<?php

use App\Http\Controllers\Auth as Auth;
use App\Http\Controllers\Backend as Backend;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('web');
});

Route::get('web', [WebController::class, 'index'])->name('web');
Route::post('web', [WebController::class, 'index'])->name('web.index');

Route::post('logout', [Auth\LoginController::class, 'logout'])->name('logout');
Route::prefix('backend')->group(function () {
    Route::get('/', [Auth\LoginController::class, 'showLoginForm']);
    Route::post('/', [Auth\LoginController::class, 'login'])->name('login');
    Route::get('forgot-password', [Auth\ResetsPasswordsController::class, 'showForgotPasswordResetForm'])->name('forgot-password');
    Route::post('sentresetpassword', [Auth\ResetsPasswordsController::class, 'getResetToken'])->name('sentresetpassword');
    Route::get('reset', [Auth\ResetsPasswordsController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [Auth\ResetsPasswordsController::class, 'resetPassword'])->name('password.update');
    Route::get('dashboard', [Backend\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('profile', Backend\ProfileController::class);
});

Route::prefix('backend')->middleware(['auth:web'])->group(function () {
    /* Role Route */
    Route::get('roles/select2', [Backend\RoleController::class, 'select2'])->name('roles.select2');
    Route::resource('roles', Backend\RoleController::class);

    /* Menu Manager Route */
    Route::resource('menu-manager', Backend\MenuManagerController::class);
    Route::post('menu-manager/changeHierarchy', [Backend\MenuManagerController::class, 'changeHierarchy'])->name('menu-manager.changeHierarchy');

    /* User Route */
    Route::resource('users', Backend\UserController::class);
    Route::post('reset-password-users', [Backend\UserController::class, 'resetpassword'])->name('users.reset-password-users');
    Route::get('change-password', [Backend\UserController::class, 'changepassword'])->name('change-password');
    Route::post('update-change-password', [Backend\UserController::class, 'updatechangepassword'])->name('update-change-password');

    /* Setting Route */
    Route::resource('setting', Backend\SettingController::class);

    /* Pages Route */
    Route::resource('pages', Backend\PagesController::class);
    Route::post('pages/changeHierarchy', [Backend\PagesController::class, 'changeHierarchy'])->name('pages.changeHierarchy');

    /* Jenis Route */
    Route::get('jenis/select2', [Backend\JenisController::class, 'select2'])->name('jenis.select2');
    Route::resource('jenis', Backend\JenisController::class);

    /* Pemilik Route */
    Route::get('pemilik/select2', [Backend\PemilikController::class, 'select2'])->name('pemilik.select2');
    Route::resource('pemilik', Backend\PemilikController::class);

    /* Kendaraan Route */
    Route::get('kendaraan/select2', [Backend\KendaraanController::class, 'select2'])->name('kendaraan.select2');
    Route::resource('kendaraan', Backend\KendaraanController::class);

    /* Penyewa Route */
    Route::get('penyewa/select2', [Backend\PenyewaController::class, 'select2'])->name('penyewa.select2');
    Route::get('penyewa/getPenyewa/{nik}', [Backend\PenyewaController::class, 'getPenyewa'])->name('penyewa.getPenyewa');
    Route::resource('penyewa', Backend\PenyewaController::class);

    /* Transaksi Route */
    Route::resource('faktur', Backend\FakturController::class);
    Route::resource('pemesanan', Backend\PemesananController::class);

    // referral
    Route::get('referral/select2', [Backend\ReferralController::class, 'select2'])->name('referral.select2');
    Route::resource('referral', Backend\ReferralController::class);
});
