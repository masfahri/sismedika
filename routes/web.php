<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AcaraController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\PegawaiController as ApiPegawai;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\Admin\PermissionController;

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
    return view('auth.login');
});

Route::get('log', [UserController::class, 'LogActivity'])->name('log');

Auth::routes();

// Route::get('/', [AuthController::class, 'showFormLogin'])->name('login');
Route::get('login', [AuthController::class, 'showFormLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('register', [AuthController::class, 'showFormRegister'])->name('register');
Route::post('register', [AuthController::class, 'register']);

Route::get('/home', [HomeController::class, 'index'])->name('home');

require __DIR__ . '/pelayan.php';
require __DIR__ . '/kasir.php';


Route::group(['middleware' => ['role:manajer', 'auth']], function () {
    Route::resource('dashboard', DashboardController::class);
    Route::name('admin.')->prefix('/admin')->group(function ()
    {
        Route::resource('permissions', PermissionController::class);
        Route::resource('roles', RoleController::class);

        Route::resource('pegawai', PegawaiController::class);
    });
});

Route::prefix('apis')->group(function (){
    Route::resource('pegawai', ApiPegawai::class);
});



Route::group(['middleware' => ['auth']], function() {
    Route::get('logout', [AuthController::class, 'logout']);

    Route::resource('acara', AcaraController::class);


});
