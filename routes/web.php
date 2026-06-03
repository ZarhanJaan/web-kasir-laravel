<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\StokController;
use App\Http\Controllers\KategoriController;


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

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // Owner and Admin only routes
    Route::middleware(['role:owner,admin'])->group(function () {
        Route::post('/setting/update-store-name', [App\Http\Controllers\SettingController::class, 'updateStoreName'])->name('setting.update-store-name');


        Route::get('/stok', [StokController::class, 'dashboard'])->name('stok.dashboard');
        Route::get('/stok/edit/{id}', [StokController::class, 'edit_stok'])->name('stok.edit');
        Route::post('/stok/update/{id}', [StokController::class, 'update_stok'])->name('stok.update');
        Route::get('/stok/add', [StokController::class, 'add'])->name('stok.add');
        Route::get('/stok/keluar', [StokController::class, 'keluar'])->name('stok.keluar');
        Route::get('/stok/keluar/add', [StokController::class, 'keluar_add'])->name('stok.keluar.add');
        Route::post('/stok/keluar/insert', [StokController::class, 'keluar_insert'])->name('stok.keluar.insert');
        Route::get('/stok/keluar/edit/{id}', [StokController::class, 'keluar_edit'])->name('stok.keluar.edit');
        Route::post('/stok/keluar/update/{id}', [StokController::class, 'keluar_update'])->name('stok.keluar.update');
        Route::get('/stok/keluar/delete/{id}', [StokController::class, 'keluar_delete'])->name('stok.keluar.delete');

        Route::get('/stok/laporan-grafik', [StokController::class, 'laporan_grafik'])->name('stok.laporan_grafik');
        Route::get('/stok/laporan-grafik/pdf/masuk', [StokController::class, 'laporan_grafik_pdf_masuk'])->name('stok.laporan_grafik_pdf_masuk');
        Route::get('/stok/laporan-grafik/pdf/keluar', [StokController::class, 'laporan_grafik_pdf_keluar'])->name('stok.laporan_grafik_pdf_keluar');

        Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori');
        Route::post('/kategori/insert', [KategoriController::class, 'insert']);
        Route::get('/kategori/delete/{id}', [KategoriController::class, 'delete']);

        Route::post('/stok/insert', [StokController::class, 'insert'])->name('stok.insert');
        Route::get('/stok/riwayat', [StokController::class, 'riwayat'])->name('stok.riwayat');
        Route::get('/stok/bahan', [StokController::class, 'bahan'])->name('stok.bahan');
        Route::post('/stok/bahan/insert', [StokController::class, 'bahan_insert'])->name('stok.bahan.insert');
        Route::get('/stok/bahan/delete/{id}', [StokController::class, 'bahan_delete'])->name('stok.bahan.delete');





        Route::get('/manajemen-user', [App\Http\Controllers\UserController::class, 'index'])->name('manajemen-user');
        Route::post('/manajemen-user/update', [App\Http\Controllers\UserController::class, 'updateRole'])->name('manajemen-user.update');
        Route::get('/manajemen-user/delete/{id}', [App\Http\Controllers\UserController::class, 'delete'])->name('manajemen-user.delete');
        
        Route::get('/setting', [App\Http\Controllers\SettingController::class, 'index'])->name('setting');
        Route::post('/setting/update-qris', [App\Http\Controllers\SettingController::class, 'updateQris'])->name('setting.update-qris');
    });

    // Routes accessible by Kasir as well

});
