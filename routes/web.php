<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;


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
    return view('auth/login');
});

Auth::routes();
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login'); // Redirect setelah logout
})->name('logout');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('/produk', [ProdukController::class, 'index'])->name('produk');
Route::get('/produk/add', [ProdukController::class, 'add']);
Route::post('/produk/insert', [ProdukController::class, 'insert']);
Route::get('/produk/edit/{id_produk}', [ProdukController::class, 'edit']);
Route::post('/produk/update/{id_produk}', [ProdukController::class, 'update']);
Route::get('/produk/delete/{id_produk}', [ProdukController::class, 'delete']);

Route::get('/user', [UserController::class, 'index'])->name('user');
Route::get('/user/add', [UserController::class, 'add']);
Route::post('/user/insert', [UserController::class, 'insert']);
Route::get('/user/edit/{id}', [UserController::class, 'edit']);
Route::post('/user/update/{id}', [UserController::class, 'update']);
Route::get('/user/delete/{id}', [UserController::class, 'delete']);

Route::get('/menu', [MenuController::class, 'index'])->name('menu');
Route::get('/menu/add', [MenuController::class, 'add']);
Route::post('/menu/insert', [MenuController::class, 'insert']);
Route::get('/menu/edit/{id_menu}', [MenuController::class, 'edit']);
Route::post('/menu/update/{id_menu}', [MenuController::class, 'update']);
Route::get('/menu/delete/{id_menu}', [MenuController::class, 'delete']);

Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan');
Route::get('/penjualan/add', [PenjualanController::class, 'add']);
Route::post('/penjualan/insert', [PenjualanController::class, 'insert']);
Route::get('/penjualan/edit/{id_penjualan}', [PenjualanController::class, 'edit']);
Route::post('/penjualan/update/{id_penjualan}', [PenjualanController::class, 'update']);
Route::get('/penjualan/delete/{id_penjualan}', [PenjualanController::class, 'delete']);

Route::get('/exportexcel', [PenjualanController::class, 'exportexcel'])->name('exportexcel');
Route::get('/exportpdf', [PenjualanController::class, 'exportpdf'])->name('exportpdf');
Route::get('/datapenjualan_tgl_pdf', [PenjualanController::class, 'datapenjualan_tgl_pdf'])->name('datapenjualan_tgl_pdf');
Route::get('/cetak_tgl_pdf/{tglawal}/{tglakhir}', [PenjualanController::class, 'cetak_tgl_pdf'])->name('cetak_tgl_pdf');
