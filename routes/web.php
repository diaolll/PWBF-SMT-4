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
use App\Http\Controllers\KantinController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\KunjunganTokoController;




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

    Route::get('/wilayah', [WilayahController::class, 'index'])->name('wilayah.index');
    Route::get('/wilayah-axios', [WilayahController::class, 'indexAxios'])->name('wilayah.axios');
    Route::get('/api/provinsi', [WilayahController::class, 'getProvinsi'])->name('api.provinsi');
    Route::get('/api/kota/{id_provinsi}', [WilayahController::class, 'getKota'])->name('api.kota');
    Route::get('/api/kecamatan/{id_kota}', [WilayahController::class, 'getKecamatan'])->name('api.kecamatan');
    Route::get('/api/kelurahan/{id_kecamatan}', [WilayahController::class, 'getKelurahan'])->name('api.kelurahan');

    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('/pos-axios', [PosController::class, 'indexAxios'])->name('pos.axios');
    Route::get('/api/barang/{kode}', [PosController::class, 'getBarang'])->name('api.barang');
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
        Route::get('/scan', [BarangController::class, 'scan'])->name('scan');
        Route::post('/', [BarangController::class, 'store'])->name('store');
        Route::delete('/{id}', [BarangController::class, 'destroy'])->name('destroy');
        Route::post('/generate-pdf', [BarangController::class, 'generatePDF'])->name('pdf');
        Route::get('/{id}/edit', [BarangController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BarangController::class, 'update'])->name('update');
    });

    /*
    |--------------------------------------------------------------------------
    | MODUL 6: KANTIN & PAYMENT GATEWAY (MIDTRANS)
    |--------------------------------------------------------------------------
    */

    Route::prefix('kantin')->name('kantin.')->group(function () {
        Route::get('/', [KantinController::class, 'index'])->name('index');
        Route::get('/menu/{id}', [KantinController::class, 'menu'])->name('menu');
        Route::get('/orders', [KantinController::class, 'orders'])->name('orders');
        Route::post('/checkout', [KantinController::class, 'checkout'])->name('checkout');
        Route::get('/sukses', [PaymentController::class, 'sukses'])->name('sukses'); // ← PaymentController
    });

    Route::get('/vendor', [AdminController::class, 'vendor'])->name('admin.vendor.index');
    Route::post('/vendor', [AdminController::class, 'storeVendor'])->name('admin.vendor.store');
    Route::get('/vendor/qrcode', [AdminController::class, 'scan'])->name('admin.vendor.qrcode');

    Route::get('/menu', [AdminController::class, 'menu'])->name('admin.menu.index');
    Route::post('/menu', [AdminController::class, 'storeMenu'])->name('admin.menu.store');

    Route::get('/pesanan', [AdminController::class, 'pesanan'])->name('admin.pesanan.index');
    // Route untuk melihat detail pesanan lunas
    Route::get('/pesanan/detail/{order_id}', [App\Http\Controllers\PaymentController::class, 'show'])->name('pesanan.detail');
    // API untuk mendapatkan data pesanan berdasarkan order_id (untuk QR Scanner)
    Route::get('/api/pesanan/{order_id}', [App\Http\Controllers\PaymentController::class, 'getPesanan'])->name('api.pesanan');

    // ← Cek status manual dari Midtrans (jurus pamungkas)
    Route::get('/pesanan/check/{order_id}', [PaymentController::class, 'checkStatus'])->name('pesanan.check');

    Route::get('/generate-sertifikat', [PDFController::class, 'generateSertifikat'])->name('pdf.sertifikat');
    Route::get('/generate-surat', [PDFController::class, 'generateSurat'])->name('pdf.surat');

    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::prefix('customer')->name('customer.')->group(function () {
        Route::get('/',         [CustomerController::class, 'index'])->name('index');
        Route::get('/tambah1',  [CustomerController::class, 'tambah1'])->name('tambah1');
        Route::post('/tambah1', [CustomerController::class, 'store1'])->name('store1');
        Route::get('/tambah2',  [CustomerController::class, 'tambah2'])->name('tambah2');
        Route::post('/tambah2', [CustomerController::class, 'store2'])->name('store2');
    });

    Route::prefix('kunjungan-toko')->name('kunjungan-toko.')->group(function () {
    Route::get('/',                     [KunjunganTokoController::class, 'index'])->name('index');
    Route::post('/toko',                [KunjunganTokoController::class, 'storeToko'])->name('toko.store');
    Route::delete('/toko/{barcode}',    [KunjunganTokoController::class, 'destroyToko'])->name('toko.destroy');
    Route::get('/toko/{barcode}',       [KunjunganTokoController::class, 'getByBarcode'])->name('toko.barcode');
    Route::post('/cek',                 [KunjunganTokoController::class, 'cekKunjungan'])->name('cek');
    });
});

// Callback Midtrans (di luar auth)
Route::post('/midtrans/callback', [PaymentController::class, 'callback'])->name('midtrans.callback');
    Route::post('/pesanan/retry/{order_id}', [KantinController::class, 'retry']);

/*
|--------------------------------------------------------------------------
| SISTEM ANTRIAN REAL-TIME (SSE)
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\AntrianGuestController;
use App\Http\Controllers\AntrianPapanController;
use App\Http\Controllers\AntrianAdminController;
use App\Http\Controllers\AntrianSSEController;

// Guest routes (public)
Route::prefix('guest')->name('guest.')->group(function () {
    Route::get('/', [AntrianGuestController::class, 'index'])->name('index');
    Route::post('/daftar', [AntrianGuestController::class, 'daftar'])->name('daftar');
    Route::get('/redirect/{nomor}/{nama}', [AntrianGuestController::class, 'redirectView'])->name('redirect');
    Route::get('/tiket/{nomor}/{nama}', [AntrianGuestController::class, 'tiket'])->name('tiket');
});

// Papan antrian (public)
Route::get('/papan', [AntrianPapanController::class, 'index'])->name('papan.index');

// SSE endpoint (public)
Route::get('/sse/antrian', [AntrianSSEController::class, 'stream'])->name('sse.antrian');

// API polling endpoint (public) - lighter alternative to SSE
Route::get('/api/antrian', [AntrianSSEController::class, 'poll'])->name('api.antrian');

// Admin routes (auth + role:admin)
Route::middleware(['auth', 'role:admin'])->prefix('antrian')->name('antrian.')->group(function () {
    Route::get('/admin', [AntrianAdminController::class, 'index'])->name('admin');
    Route::post('/tambah', [AntrianAdminController::class, 'tambah'])->name('tambah');
    Route::post('/panggil', [AntrianAdminController::class, 'panggil'])->name('panggil');
    Route::post('/terlambat/{id}', [AntrianAdminController::class, 'tandaiTerlambat'])->name('terlambat');
    Route::post('/panggil-terlambat/{id}', [AntrianAdminController::class, 'panggilTerlambat'])->name('panggil-terlambat');
    Route::post('/reset', [AntrianAdminController::class, 'reset'])->name('reset');
});
