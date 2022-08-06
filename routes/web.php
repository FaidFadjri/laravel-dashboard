<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
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

Route::get('/', [DashboardController::class, 'index'])->middleware('login');
Route::get('cabang/{wilayah}/{kondisi}/{premises}', [DashboardController::class, 'get_cabang']);
Route::get('outlet/{cabang}/{kondisi}/{premises}', [DashboardController::class, 'get_outlet']);
Route::get('datatable', [DashboardController::class, 'datatable'])->middleware('login');;

Route::group(['prefix' => 'user'], function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('load_user', [UserController::class, '_loadUser']);
    Route::post('get_user', [UserController::class, '_getUser']);
});

//------ Combobox
Route::post('load_cabang', [UserController::class, '_loadCabang']);
Route::post('load_outlet', [UserController::class, '_loadOutlet']);
Route::post('password/confirmation', [UserController::class, '_passwordConfirmation']);


//------ Auth
Route::get('login', [DashboardController::class, 'login']);
Route::get('logout', [DashboardController::class, 'logout']);
Route::post('authorization', [DashboardController::class, 'authorization']);

//------ Additional Routes
Route::get('datatable/{premises}/{kondisi}/{outlet}', [DashboardController::class, 'datatable_with_parameter']);


//------ AJAX Routes
Route::get('load_datatable', [DashboardController::class, 'load_datatable']);
Route::post('get_detail', [DashboardController::class, 'get_detail']);
Route::post('load_barchart', [DashboardController::class, 'load_barchart']);
Route::post('load_barchart', [DashboardController::class, 'load_barchart']);
