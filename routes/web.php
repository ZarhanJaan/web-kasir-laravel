<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\StokController;


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

    Route::get('/menu', [ProdukController::class, 'index'])->name('menu');
    Route::get('/menu/add', [ProdukController::class, 'add']);
    Route::post('/menu/insert', [ProdukController::class, 'insert']);
    Route::get('/menu/edit/{id_produk}', [ProdukController::class, 'edit']);
    Route::post('/menu/update/{id_produk}', [ProdukController::class, 'update']);
    Route::get('/menu/delete/{id_produk}', [ProdukController::class, 'delete']);

    Route::get('/riwayat-transaksi', [PenjualanController::class, 'index'])->name('riwayat-transaksi');
    Route::get('/riwayat-transaksi/add', [PenjualanController::class, 'add']);
    Route::post('/riwayat-transaksi/insert', [PenjualanController::class, 'insert']);
    Route::get('/riwayat-transaksi/edit/{id_penjualan}', [PenjualanController::class, 'edit']);
    Route::post('/riwayat-transaksi/update/{id_penjualan}', [PenjualanController::class, 'update']);
    Route::get('/riwayat-transaksi/delete/{id_penjualan}', [PenjualanController::class, 'delete']);
    Route::get('/kasir', [PenjualanController::class, 'pos'])->name('kasir');
    Route::post('/kasir/insert', [PenjualanController::class, 'pos_insert'])->name('kasir.insert');
    Route::get('/struk/{id_penjualan}', [PenjualanController::class, 'struk'])->name('struk');

    Route::get('/exportexcel', [PenjualanController::class, 'exportexcel'])->name('exportexcel');
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
    Route::get('/stok/bahan/delete/{id}', [StokController::class, 'bahan_delete'])->name('stok.bahan.delete');

    Route::get('/resep', [App\Http\Controllers\ResepController::class, 'index'])->name('resep');
    Route::get('/resep/add', [ProdukController::class, 'add'])->name('resep.add');
    Route::post('/resep/insert', [ProdukController::class, 'insert'])->name('resep.insert');
    Route::get('/resep/edit/{id_menu}', [App\Http\Controllers\ResepController::class, 'edit']);
    Route::post('/resep/update/{id_menu}', [App\Http\Controllers\ResepController::class, 'update']);
    Route::post('/resep/item/add', [App\Http\Controllers\ResepController::class, 'add_item']);
    Route::get('/resep/item/delete/{id_resep}', [App\Http\Controllers\ResepController::class, 'delete_item']);

    Route::get('/datapenjualan_tgl_pdf', [PenjualanController::class, 'datapenjualan_tgl_pdf'])->name('datapenjualan_tgl_pdf');
    Route::get('/cetak_tgl_pdf/{tglawal}/{tglakhir}', [PenjualanController::class, 'cetak_tgl_pdf'])->name('cetak_tgl_pdf');
    Route::get('/laporan', [PenjualanController::class, 'laporan'])->name('laporan.index');
    Route::get('/export-terlaris-pdf', [PenjualanController::class, 'exportTerlarisPdf'])->name('export-terlaris-pdf');
    Route::get('/export-stok-pdf', [PenjualanController::class, 'exportStokPdf'])->name('export-stok-pdf');

    Route::get('/manajemen-user', function () {
        return view('manajemen_user');
    })->name('manajemen-user');
});
