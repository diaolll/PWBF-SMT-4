<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\ModulController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\PosController;


Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/', [LoginController::class, 'login']);

    Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

    Route::get('/otp-verify', function () {
        return view('auth.otp');
    })->name('otp.view');
    Route::post('/otp-verify', [GoogleController::class, 'verifyOtp'])->name('otp.verify');
});

Auth::routes(['login' => false]);

Route::middleware(['auth'])->group(function () {
    Route::get('/Dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/modul/table-datatable', [ModulController::class,'tableDatatable']);
    Route::get('/modul/select-kota', [ModulController::class,'selectKota']);
    Route::get('/modul/tabel-html', [ModulController::class,'TabelHtml']);

        /*
    |--------------------------------------------------------------------------
    | BAGIAN 1: Routes untuk Dropdown Wilayah (Provinsi, Kota, Kecamatan, Kelurahan)
    |--------------------------------------------------------------------------
    */

    // Halaman utama wilayah (versi jQuery AJAX)
    Route::get('/wilayah', [WilayahController::class, 'index'])->name('wilayah.index');
    
    // Halaman utama wilayah (versi Axios)
    Route::get('/wilayah-axios', [WilayahController::class, 'indexAxios'])->name('wilayah.axios');
    
    // API endpoints untuk dropdown wilayah (dipakai oleh AJAX & Axios)
    Route::get('/api/provinsi', [WilayahController::class, 'getProvinsi'])->name('api.provinsi');
    Route::get('/api/kota/{id_provinsi}', [WilayahController::class, 'getKota'])->name('api.kota');
    Route::get('/api/kecamatan/{id_kota}', [WilayahController::class, 'getKecamatan'])->name('api.kecamatan');
    Route::get('/api/kelurahan/{id_kecamatan}', [WilayahController::class, 'getKelurahan'])->name('api.kelurahan');
    
    /*
    |--------------------------------------------------------------------------
    | BAGIAN 2: Routes untuk Point of Sales (POS) / Kasir
    |--------------------------------------------------------------------------
    */
    
    // Halaman POS (versi jQuery AJAX)
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    
    // Halaman POS (versi Axios)
    Route::get('/pos-axios', [PosController::class, 'indexAxios'])->name('pos.axios');
    
    // API: Cari barang berdasarkan kode
    Route::get('/api/barang/{kode}', [PosController::class, 'getBarang'])->name('api.barang');
    
    // API: Simpan transaksi penjualan
    Route::post('/api/bayar', [PosController::class, 'bayar'])->name('api.bayar');

    Route::prefix('Buku')->name('buku.')->group(function () {
        Route::get('/', [BukuController::class, 'index'])->name('index');
        Route::get('/create', [BukuController::class, 'create'])->name('create');
        Route::post('/', [BukuController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [BukuController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BukuController::class, 'update'])->name('update');
        Route::delete('/{id}', [BukuController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('Kategori')->name('kategori.')->group(function () {
        Route::get('/', [KategoriController::class, 'index'])->name('index');
        Route::get('/create', [KategoriController::class, 'create'])->name('create');
        Route::post('/', [KategoriController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [KategoriController::class, 'edit'])->name('edit');
        Route::put('/{id}', [KategoriController::class, 'update'])->name('update');
        Route::delete('/{id}', [KategoriController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('barang')->name('barang.')->group(function () {
        Route::get('/', [BarangController::class, 'index'])->name('index');
        Route::post('/', [BarangController::class, 'store'])->name('store');
        Route::delete('/{id}', [BarangController::class, 'destroy'])->name('destroy');
        Route::post('/generate-pdf', [BarangController::class, 'generatePDF'])->name('pdf'); // 

        Route::get('/{id}/edit', [BarangController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BarangController::class, 'update'])->name('update');

        
    });

    Route::get('/generate-sertifikat', [PDFController::class, 'generateSertifikat'])->name('pdf.sertifikat');
    Route::get('/generate-surat', [PDFController::class, 'generateSurat'])->name('pdf.surat');

    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

});