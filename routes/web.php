<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth as Auth;
use App\Http\Controllers\WebController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Backend as Backend;

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
    /* Dashboard */
    Route::get('graph', [Backend\DashboardController::class, 'graph'])->name('dashboard.graph');
    Route::get('checkin', [Backend\DashboardController::class, 'checkin'])->name('dashboard.checkin');

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
    Route::get('kendaraan/status', [Backend\KendaraanController::class, 'status'])->name('kendaraan.status');
    Route::resource('kendaraan', Backend\KendaraanController::class);

    /* Penyewa Route */
    Route::get('penyewa/select2', [Backend\PenyewaController::class, 'select2'])->name('penyewa.select2');
    Route::get('penyewa/getPenyewa/{nik}', [Backend\PenyewaController::class, 'getPenyewa'])->name('penyewa.getPenyewa');
    Route::resource('penyewa', Backend\PenyewaController::class);

    /* Transaksi Route */
    Route::resource('faktur', Backend\FakturController::class);

    //booking/pemesanan
    Route::put('pemesanan/proses/{penyewaan}', [Backend\PemesananController::class, 'proses'])->name('pemesanan.proses');
    Route::resource('pemesanan', Backend\PemesananController::class);
    Route::get('pemesanan/create/{id_kendaraan}/{tanggal}', [Backend\PemesananController::class, 'create'])->name('pemesanan.createId');


    // referral
    Route::get('referral/select2', [Backend\ReferralController::class, 'select2'])->name('referral.select2');
    Route::resource('referral', Backend\ReferralController::class);

    //invoice
    Route::resource('invoice', Backend\InvoiceController::class);
    Route::get('invoice/{id}/cetak', [Backend\InvoiceController::class, 'cetak'])->name('invoice.cetak');
    Route::get('invoice-sewa/{id}/cetak', [Backend\InvoiceController::class, 'sewaCetak'])->name('invoice-sewa.cetak');

    //laporan
    Route::get('laporan-harian', [Backend\LaporanController::class, 'harian_index'])->name('laporan.harian');
    Route::get('laporan-bulanan', [Backend\LaporanController::class, 'bulanan_index'])->name('laporan.bulanan');
    Route::get('laporan-referral', [Backend\LaporanController::class, 'referral_index'])->name('laporan.referral');

    //booking/pemesanan
    Route::put('penyewaan/proses/{penyewaan}', [Backend\PenyewaanController::class, 'proses'])->name('penyewaan.proses');
    Route::get('penyewaan/create/{id_kendaraan}/{tanggal}', [Backend\PenyewaanController::class, 'create'])->name('penyewaan.createId');
    Route::resource('penyewaan', Backend\PenyewaanController::class);
    Route::get('/penyewaan/edit/{id}/{id_kendaraan}', [Backend\PenyewaanController::class, 'edit'])->name('penyewaan.edit_sewa');


});
