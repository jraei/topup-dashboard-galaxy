<?php

use Inertia\Inertia;
use App\Models\Banner;
use App\Models\Produk;
use App\Models\WebConfig;
use App\Http\Controllers\ApiDocs;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MoogoldController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProfitPaketLayananController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\TripayController;
use App\Http\Controllers\Admin\CronjobController;
use App\Http\Controllers\Admin\DepositController;
use App\Http\Controllers\Admin\LayananController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\ProviderController;
use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\Admin\PayMethodController;
use App\Http\Controllers\Admin\PembelianController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\PaketLayananController;
use App\Http\Controllers\Admin\ProfitProdukController;
use App\Http\Controllers\Admin\FlashsaleItemController;
use App\Http\Controllers\Admin\ItemThumbnailController;
use App\Http\Controllers\Admin\FlashsaleEventController;
use App\Http\Controllers\Admin\PaymentProviderController;
use App\Http\Controllers\Admin\ProdukInputFieldController;
use App\Http\Controllers\Admin\ProdukInputOptionController;

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
// Calculator Routes
Route::get('/calculator/winrate', [CalculatorController::class, 'winrate'])->name('calculator.winrate');
Route::get('/calculator/magic-wheel', [CalculatorController::class, 'magicWheel'])->name('calculator.magic-wheel');
Route::get('/calculator/zodiac', [CalculatorController::class, 'zodiac'])->name('calculator.zodiac');


Route::get('/', [IndexController::class, 'index'])->name('index');
Route::get('/leaderboard', [IndexController::class, 'leaderboard'])->name('leaderboard');
Route::get('/cek-transaksi', [IndexController::class, 'cekTransaksi'])->name('cek-transaksi');
Route::get('/term-of-service', [IndexController::class, 'termOfService'])->name('term-of-service');

// API Documentation
Route::middleware('auth', 'h2h')->group(function () {
    Route::get('/api-docs', [ApiController::class, 'docs'])->name('api-docs');
    Route::post('/api/update-ip-whitelist', [ApiController::class, 'updateIpWhitelist'])->name('api.update-ip-whitelist');
});


// Order Processing Routes
Route::get('/order/{produk:slug}', [OrderController::class, 'index'])->name('order.index');
Route::post('/order/confirm', [OrderController::class, 'confirmOrder'])->name('order.confirm');
Route::post('/order/process', [OrderController::class, 'processOrder'])->name('order.process');

// Order Invoice
Route::get('/order/invoice/{order_id}', [OrderController::class, 'invoice'])->name('order.invoice');

// Cronjob routes
Route::get('/getMoogold', [CronjobController::class, 'getMoogold'])->name('cronjob.getMoogold');
Route::get('/getDigiflazz', [CronjobController::class, 'getDigiflazz'])->name('cronjob.getDigiflazz');
Route::get('/statusMoogold', [CronjobController::class, 'statusMoogold'])->name('cronjob.statusMoogold');
Route::get('/statusNaelstore', [CronjobController::class, 'statusNaelstore'])->name('cronjob.statusNaelstore');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard
    Route::get('/dashboard/balance', [DashboardController::class, 'balance'])->name('dashboard.balance');
    Route::get('/dashboard/topup', [DashboardController::class, 'topup'])->name('dashboard.topup');
    Route::post('/dashboard/topup/process', [DashboardController::class, 'processTopup'])->name('dashboard.process.topup');
    Route::get('/dashboard/topup/invoice/{deposit:deposit_id}', [DashboardController::class, 'showInvoice'])->name('invoice.topup');
    Route::get('/dashboard/transactions', [DashboardController::class, 'transactions'])->name('dashboard.transactions');
    Route::get('/dashboard/mutations', [DashboardController::class, 'mutations'])->name('dashboard.mutations');
    Route::get('/dashboard/affiliate', [DashboardController::class, 'affiliate'])->name('dashboard.affiliate');
});

// Dashboard routes - use auth middleware
Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/balance', [DashboardController::class, 'balance'])->name('dashboard.balance');
    Route::get('/transactions', [DashboardController::class, 'transactions'])->name('dashboard.transactions');
    Route::get('/transactions/export', [DashboardController::class, 'exportTransactions'])->name('dashboard.transactions.export');
    Route::get('/mutations', [DashboardController::class, 'mutations'])->name('dashboard.mutations');
    Route::get('/affiliate', [DashboardController::class, 'affiliate'])->name('dashboard.affiliate');
    Route::get('/topup', [DashboardController::class, 'topup'])->name('dashboard.topup');
});

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');

    Route::get('dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('dashboard/export', [AdminController::class, 'exportDashboard'])->name('admin.dashboard.export');

    // Dashboard API endpoints
    Route::get('dashboard/products', [AdminController::class, 'getProducts']);
    Route::get('dashboard/product-services/{productId?}', [AdminController::class, 'getProductServices']);
    Route::get('dashboard/flashsales', [AdminController::class, 'getFlashsales']);
    Route::get('dashboard/vouchers', [AdminController::class, 'getVouchers']);


    // Settings routes
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::patch('/settings/general', [AdminController::class, 'updateGeneralSettings'])->name('admin.settings.general');
    Route::patch('/settings/appearance', [AdminController::class, 'updateAppearance'])->name('admin.settings.appearance');
    Route::patch('/settings/api', [AdminController::class, 'updateApiConnections'])->name('admin.settings.api');
    Route::patch('/settings/popup', [AdminController::class, 'updatePopupSettings'])->name('admin.settings.popup');
    Route::delete('/settings/logo/{field}', [AdminController::class, 'deleteLogo'])->name('admin.settings.logo.delete');

    // banner management
    Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
    Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
    Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');

    // Item Thumbnail management
    Route::resource('item-thumbnails', ItemThumbnailController::class);
    Route::post('item-thumbnails/bulk-assign', [ItemThumbnailController::class, 'bulkAssign'])->name('item-thumbnails.bulk-assign');
    Route::patch('item-thumbnails/{itemThumbnail}/toggle-static', [ItemThumbnailController::class, 'toggleStatic'])->name('item-thumbnails.toggle-static');
    Route::patch('item-thumbnails/{itemThumbnail}/toggle-default', [ItemThumbnailController::class, 'toggleDefault'])->name('item-thumbnails.toggle-default');

    // voucher management
    Route::resource('vouchers', VoucherController::class);

    // pay method management
    Route::resource('pay-methods', PayMethodController::class);

    // User Role Management
    Route::resource('user-roles', UserRoleController::class);
    Route::patch('user-roles/{userRole}/toggle-default', [UserRoleController::class, 'toggleDefault'])->name('user-roles.toggle-default');

    // User Management
    Route::resource('users', UserController::class);

    // Deposit Management - using resource controller except edit and update
    Route::resource('/deposits', DepositController::class)->except(['edit', 'update']);

    // Category Management with Moogold Integration
    Route::resource('/categories', KategoriController::class);
    Route::post('/categories/{id}/link-moogold', [KategoriController::class, 'linkMoogold'])->name('categories.link-moogold');
    Route::get('/categories/{id}/available-products', [KategoriController::class, 'getAvailableProducts'])->name('categories.available-products');
    Route::post('/categories/{id}/bulk-assign', [KategoriController::class, 'bulkAssign'])->name('categories.bulk-assign');

    // Placeholder routes for all other admin pages
    Route::resource('/products', ProdukController::class);
    Route::post('/products/{id}/validation', [ProdukController::class, 'updateValidation'])->name('products.updateValidation');
    Route::delete('/products/deleteProductsByProvider/{provider}', [ProdukController::class, 'deleteProductsByProvider'])->name('products.deleteProduks');
    Route::patch('/products/togglePopuler/{produk}', [ProdukController::class, 'togglePopuler'])->name('products.togglePopuler');
    Route::post('/services/getService/{provider}', [ProdukController::class, 'getProductsByProvider'])->name('products.getProductsByProvider');

    // Service Management
    Route::resource('/services', LayananController::class);
    Route::post('/services/getServices/{provider}', [LayananController::class, 'getServicesByProvider'])->name('services.getServicesByProvider');
    Route::delete('/services/deleteServicesByProvider/{provider}', [LayananController::class, 'deleteLayanan'])->name('services.deleteLayanans');

    // Provider Management
    Route::resource('/providers', ProviderController::class);
    Route::patch('/providers/{id}/rate-kurs', [ProviderController::class, 'updateRateKurs'])->name('providers.updateRateKurs');
    Route::post('providers/getBalances/{provider}', [ProviderController::class, 'getBalancesByProvider'])->name('providers.getBalancesByProvider');
    Route::post('providers/syncHargaLayanan', [ProviderController::class, 'syncHargaLayanan'])->name('providers.syncHargaLayanan');


    // Product Input Field Management
    Route::resource('produk-input-fields', ProdukInputFieldController::class)->names('admin.produk-input-fields');
    Route::post('produk-input-fields/update-order', [ProdukInputFieldController::class, 'updateOrder'])->name('admin.produk-input-fields.update-order');

    // Product Input Option Management
    Route::resource('produk-input-options', ProdukInputOptionController::class)->names('admin.produk-input-options');
    Route::post('produk-input-options/bulk', [ProdukInputOptionController::class, 'bulkStore'])->name('admin.produk-input-options.bulk-store');

    // Profit Produk routes
    Route::resource('profit-produks', ProfitProdukController::class);
    Route::post('profit-produks/bulk-store', [ProfitProdukController::class, 'bulkStore'])->name('profit-produks.bulk-store');
    Route::post('profit-produks/preview', [ProfitProdukController::class, 'preview'])->name('profit-produks.preview');

    // Profit Paket Layanan routes
    Route::resource('profit-paket-layanans', ProfitPaketLayananController::class);
    Route::post('profit-paket-layanans/bulk-store', [ProfitPaketLayananController::class, 'bulkStore'])->name('profit-paket-layanans.bulk-store');
    Route::post('profit-paket-layanans/bulk-update', [ProfitPaketLayananController::class, 'bulkUpdate'])->name('profit-paket-layanans.bulk-update');
    Route::post('profit-paket-layanans/preview', [ProfitPaketLayananController::class, 'preview'])->name('profit-paket-layanans.preview');

    Route::get('/payment-providers', [PaymentProviderController::class, 'index'])->name('admin.payment-providers');
    Route::patch('/payment-providers/{id}', [PaymentProviderController::class, 'update'])->name('payment-providers.update');
    Route::post('/payment-providers/getMethods/{id}', [PayMethodController::class, 'getMethodsByProvider'])->name('payment-providers.get-methods-by-provider');

    // Flash Sale Events
    Route::resource('flashsale-events', FlashsaleEventController::class);
    Route::patch('flashsale-events/{id}/toggle-status', [FlashsaleEventController::class, 'toggleStatus'])->name('flashsale-events.toggle-status');

    // Flash Sale Items
    Route::post('flashsale-items/bulk-assign', [FlashsaleItemController::class, 'bulkAssign'])->name('flashsale-items.bulk-assign');
    Route::delete('flashsale-items/bulk-delete', [FlashsaleItemController::class, 'bulkDelete'])->name('flashsale-items.bulk-delete');
    Route::get('flashsale-items/available-services', [FlashsaleItemController::class, 'availableServices'])->name('flashsale-items.available-services');
    Route::resource('flashsale-items', FlashsaleItemController::class);

    // Pembelian Management (Purchases)
    Route::resource('pembelians', PembelianController::class)->except(['create', 'edit', 'update']);
    Route::patch('pembelians/{id}/process', [PembelianController::class, 'process'])->name('pembelians.process');

    // Pembayaran Management (Payments)
    Route::resource('pembayarans', PembayaranController::class)->except(['create', 'edit', 'update', 'store']);

    // PaketLayanan routes
    Route::resource('paket-layanans', PaketLayananController::class);
    Route::get('paket-layanans/services/available', [PaketLayananController::class, 'getAvailableServices'])->name('paket-layanans.available-services');
    Route::get('paket-layanans/{id}/services', [PaketLayananController::class, 'getPackageServices'])->name('paket-layanans.package-services');
    
    // Fusion services routes
    Route::post('paket-layanans/{packageId}/fusion-services', [PaketLayananController::class, 'storeFusionService'])
        ->name('paket-layanans.fusion-services.store');
    Route::put('paket-layanans/{packageId}/fusion-services/{fusionId}', [PaketLayananController::class, 'updateFusionService'])
        ->name('paket-layanans.fusion-services.update');
    Route::delete('paket-layanans/{packageId}/fusion-services/{fusionId}', [PaketLayananController::class, 'destroyFusionService'])
        ->name('paket-layanans.fusion-services.destroy');
    Route::get('paket-layanans/{packageId}/fusion-services', [PaketLayananController::class, 'getFusionServices'])
        ->name('paket-layanans.fusion-services.index');
});

require __DIR__ . '/auth.php';