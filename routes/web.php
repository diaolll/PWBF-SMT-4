<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Halaman awal langsung form login
Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');

Auth::routes();

//route semua bisa akses
Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [App\Http\Controllers\Auth\LoginController::class, 'login']);

// ini route wajib login
Route::middleware(['auth'])->group(function () {
    Route::get('/Dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/Buku', [App\Http\Controllers\BukuController::class, 'index'])->name('buku.index');
    Route::get('/Buku/create', [App\Http\Controllers\BukuController::class, 'create'])->name('buku.create');
    Route::post('/Buku', [App\Http\Controllers\BukuController::class, 'store'])->name('buku.store');
    Route::get('/Buku/{id}/edit', [App\Http\Controllers\BukuController::class, 'edit'])->name('buku.edit');
    Route::put('/Buku/{id}', [App\Http\Controllers\BukuController::class, 'update'])->name('buku.update');
    Route::delete('/Buku/{id}', [App\Http\Controllers\BukuController::class, 'destroy'])->name('buku.destroy');
    


    Route::get('/Kategori', [App\Http\Controllers\KategoriController::class, 'index'])->name('kategori.index');
    Route::get('/Kategori/create', [App\Http\Controllers\KategoriController::class, 'create'])->name('kategori.create');
    Route::post('/Kategori', [App\Http\Controllers\KategoriController::class, 'store'])->name('kategori.store');
    Route::get('/Kategori/{id}/edit', [App\Http\Controllers\KategoriController::class, 'edit'])->name('kategori.edit');
    Route::put('/Kategori/{id}', [App\Http\Controllers\KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/Kategori/{id}', [App\Http\Controllers\KategoriController::class, 'destroy'])->name('kategori.destroy');

    Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
});

