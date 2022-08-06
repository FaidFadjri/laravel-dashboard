<?php

use App\Http\Controllers\DashboardController;
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
