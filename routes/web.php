<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PenjualanController;
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

        Route::get('/menu/add', [ProdukController::class, 'add']);
        Route::post('/menu/insert', [ProdukController::class, 'insert']);
        Route::get('/menu/edit/{id_produk}', [ProdukController::class, 'edit']);
        Route::post('/menu/update/{id_produk}', [ProdukController::class, 'update']);
        Route::get('/menu/delete/{id_produk}', [ProdukController::class, 'delete']);

        Route::get('/riwayat-transaksi/add', [PenjualanController::class, 'add']);
        Route::post('/riwayat-transaksi/insert', [PenjualanController::class, 'insert']);
        Route::get('/riwayat-transaksi/edit/{id_penjualan}', [PenjualanController::class, 'edit']);
        Route::post('/riwayat-transaksi/update/{id_penjualan}', [PenjualanController::class, 'update']);
        Route::get('/riwayat-transaksi/delete/{id_penjualan}', [PenjualanController::class, 'delete']);

        Route::get('/cetakexcel', [PenjualanController::class, 'cetakexcel_page'])->name('cetakexcel');
        Route::get('/exportexcel', [PenjualanController::class, 'exportexcel'])->name('exportexcel');
        Route::get('/exportexcel-tanggal', [PenjualanController::class, 'exportexcel_tanggal'])->name('exportexcel-tanggal');
        Route::get('/exportpdf', [PenjualanController::class, 'exportpdf'])->name('exportpdf');

        Route::get('/stok', [StokController::class, 'dashboard'])->name('stok.dashboard');
        Route::get('/stok/add', [StokController::class, 'add'])->name('stok.add');
        Route::get('/stok/keluar', [StokController::class, 'keluar'])->name('stok.keluar');
        Route::get('/stok/keluar/add', [StokController::class, 'keluar_add'])->name('stok.keluar.add');
        Route::post('/stok/keluar/insert', [StokController::class, 'keluar_insert'])->name('stok.keluar.insert');
        Route::get('/stok/keluar/edit/{id}', [StokController::class, 'keluar_edit']);
        Route::post('/stok/keluar/update/{id}', [StokController::class, 'keluar_update']);
        Route::get('/stok/keluar/delete/{id}', [StokController::class, 'keluar_delete']);
        Route::post('/stok/insert', [StokController::class, 'insert'])->name('stok.insert');
        Route::get('/stok/riwayat', [StokController::class, 'riwayat'])->name('stok.riwayat');
        Route::get('/stok/bahan', [StokController::class, 'bahan'])->name('stok.bahan');
        Route::post('/stok/bahan/insert', [StokController::class, 'bahan_insert'])->name('stok.bahan.insert');
        Route::get('/stok/bahan/edit/{id}', [StokController::class, 'bahan_edit'])->name('stok.bahan.edit');
        Route::post('/stok/bahan/update/{id}', [StokController::class, 'bahan_update'])->name('stok.bahan.update');
        Route::get('/stok/bahan/delete/{id}', [StokController::class, 'bahan_delete'])->name('stok.bahan.delete');

        Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori');
        Route::post('/kategori/insert', [KategoriController::class, 'insert']);
        Route::get('/kategori/delete/{id}', [KategoriController::class, 'delete']);

        Route::get('/resep', [App\Http\Controllers\ResepController::class, 'index'])->name('resep');
        Route::get('/resep/add', [ProdukController::class, 'add'])->name('resep.add');
        Route::post('/resep/insert', [ProdukController::class, 'insert'])->name('resep.insert');
        Route::get('/resep/edit/{id_menu}', [App\Http\Controllers\ResepController::class, 'edit']);
        Route::post('/resep/update/{id_menu}', [App\Http\Controllers\ResepController::class, 'update']);
        Route::post('/resep/item/add', [App\Http\Controllers\ResepController::class, 'add_item']);
        Route::get('/resep/item/delete/{id_resep}', [App\Http\Controllers\ResepController::class, 'delete_item']);

        Route::get('/datapenjualan_tgl_pdf', [PenjualanController::class, 'datapenjualan_tgl_pdf'])->name('datapenjualan_tgl_pdf');
        Route::get('/cetak_tgl_pdf/{tanggal}/{jamawal}/{jamakhir}', [PenjualanController::class, 'cetak_tgl_pdf'])->name('cetak_tgl_pdf');
        Route::get('/laporan', [PenjualanController::class, 'laporan'])->name('laporan.index');
        Route::get('/export-terlaris-pdf', [PenjualanController::class, 'exportTerlarisPdf'])->name('export-terlaris-pdf');
        Route::get('/export-stok-pdf', [PenjualanController::class, 'exportStokPdf'])->name('export-stok-pdf');
        Route::get('/export-stok-masuk-pdf', [PenjualanController::class, 'exportStokMasukPdf'])->name('export-stok-masuk-pdf');
        Route::get('/export-stok-keluar-pdf', [PenjualanController::class, 'exportStokKeluarPdf'])->name('export-stok-keluar-pdf');
        Route::get('/get-weekly-stok', [PenjualanController::class, 'getWeeklyStok'])->name('get-weekly-stok');

        Route::get('/manajemen-user', [App\Http\Controllers\UserController::class, 'index'])->name('manajemen-user');
        Route::post('/manajemen-user/update', [App\Http\Controllers\UserController::class, 'updateRole'])->name('manajemen-user.update');
        Route::get('/manajemen-user/delete/{id}', [App\Http\Controllers\UserController::class, 'delete'])->name('manajemen-user.delete');
        
        Route::get('/setting', [App\Http\Controllers\SettingController::class, 'index'])->name('setting');
        Route::post('/setting/update-qris', [App\Http\Controllers\SettingController::class, 'updateQris'])->name('setting.update-qris');
    });

    // Routes accessible by Kasir as well
    Route::middleware(['role:owner,admin,kasir'])->group(function () {
        Route::get('/menu', [ProdukController::class, 'index'])->name('menu');
        Route::get('/riwayat-transaksi', [PenjualanController::class, 'index'])->name('riwayat-transaksi');
        Route::get('/kasir', [PenjualanController::class, 'pos'])->name('kasir');
        Route::post('/kasir/insert', [PenjualanController::class, 'pos_insert'])->name('kasir.insert');
        Route::get('/struk/{id_penjualan}', [PenjualanController::class, 'struk'])->name('struk');
    });
});
