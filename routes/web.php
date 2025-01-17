<?php

use App\Http\Controllers\DepositController;
use GuzzleHttp\Client;
use App\Models\SubKategori;
use GuzzleHttp\Psr7\Request;
use Gonon\Digiflazz\Digiflazz;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PayMethodController;
use App\Http\Controllers\SubKategoriController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.index');
    })->name('dashboard');

    Route::get('produk/getProduk', [ProdukController::class, 'getProduk']);
    Route::resource('produk', ProdukController::class);


    Route::resource('kategori', KategoriController::class);
    Route::resource('sub-kategori', SubKategoriController::class);

    Route::get('layanan/getLayanan', [LayananController::class, 'getService'])->name('layanan.getService');
    Route::resource('layanan', LayananController::class);

    // Route::get('user/{id}', [UserController::class, 'show']);
    Route::resource('user', UserController::class);

    Route::get('deposit', [DepositController::class, 'index'])->name('deposit');

    Route::get('pay-method/getMethod', [PayMethodController::class, 'getMethod'])->name('pay-method.getMethod');
    Route::resource('pay-method', PayMethodController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
