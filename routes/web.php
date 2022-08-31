<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Export;
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

Route::get('test', [DashboardController::class, 'test']);
Route::get('/', [DashboardController::class, 'index'])->middleware('login');
Route::get('cabang/{wilayah}/{kondisi}/{premises}', [DashboardController::class, 'get_cabang']);
Route::get('outlet/{cabang}/{kondisi}/{premises}', [DashboardController::class, 'get_outlet']);
Route::get('datatable', [DashboardController::class, 'datatable'])->middleware('login');
Route::get('report', [DashboardController::class, 'report'])->middleware('login');

Route::post('load_premisesdata', [DashboardController::class, '_getPremisesData']);

Route::group(['prefix' => 'user'], function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('load_user', [UserController::class, '_loadUser']);
    Route::post('get_user', [UserController::class, '_getUser']);
    Route::post('save', [UserController::class, '_saveUser']);
});

//------ Combobox
Route::post('load_cabang', [UserController::class, '_loadCabang']);
Route::post('load_outlet', [UserController::class, '_loadOutlet']);
Route::post('load_outlet2', [UserController::class, '_loadOutlet2']);
Route::post('password/confirmation', [UserController::class, '_passwordConfirmation']);

//---- Export
Route::post('export', [Export::class, 'index']);
Route::get('export/test', [Export::class, 'createExcel']);

//------ Auth
Route::get('login', [DashboardController::class, 'login']);
Route::get('logout', [DashboardController::class, 'logout']);
Route::post('authorization', [DashboardController::class, 'authorization']);

//------ Additional Routes
Route::get('datatable/{premises}/{kondisi}/{outlet}', [DashboardController::class, 'datatable_with_parameter']);


//------ AJAX Routes
Route::get('load_datatable', [DashboardController::class, 'load_datatable']);
Route::get('load_report', [DashboardController::class, 'load_report']);
Route::post('get_detail', [DashboardController::class, 'get_detail']);
Route::post('load_barchart', [DashboardController::class, 'load_barchart']);

Route::get('zip', [Export::class, 'toZip']);

//---- Update 26 Agustus 2022 SMW Report
Route::get('dashboard/smw/{email}/{password}', [DashboardController::class, 'dashboard_smw']);
