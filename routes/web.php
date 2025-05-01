
<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;

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

Route::get('/', [IndexController::class, 'index']);

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/transactions', [DashboardController::class, 'transactions'])->name('dashboard.transactions');
        Route::get('/mutations', [DashboardController::class, 'mutations'])->name('dashboard.mutations');
        Route::get('/balance', [DashboardController::class, 'balance'])->name('dashboard.balance');
        Route::get('/topup', [DashboardController::class, 'topup'])->name('dashboard.topup');
        Route::get('/affiliate', [DashboardController::class, 'affiliate'])->name('dashboard.affiliate');
    });
});

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function() {
    Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('admin.index');
    
    // Admin Resources
    Route::resource('users', App\Http\Controllers\Admin\UserController::class, ['as' => 'admin']);
    Route::resource('kategoris', App\Http\Controllers\Admin\KategoriController::class, ['as' => 'admin']);
    Route::resource('produks', App\Http\Controllers\Admin\ProdukController::class, ['as' => 'admin']);
    Route::resource('layanans', App\Http\Controllers\Admin\LayananController::class, ['as' => 'admin']);
    Route::resource('pay-methods', App\Http\Controllers\Admin\PayMethodController::class, ['as' => 'admin']);
    Route::resource('payment-providers', App\Http\Controllers\Admin\PaymentProviderController::class, ['as' => 'admin']);
    Route::resource('pembelians', App\Http\Controllers\Admin\PembelianController::class, ['as' => 'admin']);
    Route::resource('pembayarans', App\Http\Controllers\Admin\PembayaranController::class, ['as' => 'admin']);
    Route::resource('deposits', App\Http\Controllers\Admin\DepositController::class, ['as' => 'admin']);
    Route::resource('providers', App\Http\Controllers\Admin\ProviderController::class, ['as' => 'admin']);
    Route::resource('profit-produks', App\Http\Controllers\Admin\ProfitProdukController::class, ['as' => 'admin']);
    Route::resource('banners', App\Http\Controllers\Admin\BannerController::class, ['as' => 'admin']);
    Route::resource('web-configs', App\Http\Controllers\Admin\WebConfigController::class, ['as' => 'admin']);
    Route::resource('vouchers', App\Http\Controllers\Admin\VoucherController::class, ['as' => 'admin']);
    Route::resource('flashsale-events', App\Http\Controllers\Admin\FlashsaleEventController::class, ['as' => 'admin']);
    Route::resource('flashsale-items', App\Http\Controllers\Admin\FlashsaleItemController::class, ['as' => 'admin']);
    Route::resource('item-thumbnails', App\Http\Controllers\Admin\ItemThumbnailController::class, ['as' => 'admin']);
    Route::resource('produk-input-fields', App\Http\Controllers\Admin\ProdukInputFieldController::class, ['as' => 'admin']);
    Route::resource('produk-input-options', App\Http\Controllers\Admin\ProdukInputOptionController::class, ['as' => 'admin']);
});

// Order routes
Route::get('/order/{produk:slug}', [OrderController::class, 'index'])->name('order.index');
Route::get('/payment/{order_id}', [OrderController::class, 'showPayment'])->name('payment.show');

// API routes for order processing
Route::prefix('api')->group(function() {
    Route::post('/order/confirm', [OrderController::class, 'confirmOrder'])->name('api.order.confirm');
    Route::post('/order/process', [OrderController::class, 'processOrder'])->name('api.order.process');
    Route::post('/order/validate-voucher', [OrderController::class, 'validateVoucher'])->name('api.order.validate-voucher');
});

require __DIR__.'/auth.php';
