<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PDFController;

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

    Route::get('/generate-sertifikat', [PDFController::class, 'generateSertifikat'])->name('pdf.sertifikat');
    Route::get('/generate-surat', [PDFController::class, 'generateSurat'])->name('pdf.surat');

    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
});