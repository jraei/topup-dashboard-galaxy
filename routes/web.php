
<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::get('/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::get('/banners', [AdminController::class, 'banners'])->name('admin.banners');
    
    // User Management
    Route::resource('users', UserController::class)->names([
        'index' => 'userManage',
    ]);
    
    // Placeholder routes for all other admin pages
    Route::get('/products', function() { return Inertia::render('Admin/Dashboard'); })->name('admin.products');
    Route::get('/services', function() { return Inertia::render('Admin/Dashboard'); })->name('admin.services');
    Route::get('/profit', function() { return Inertia::render('Admin/Dashboard'); })->name('admin.profit');
    Route::get('/payment-providers', function() { return Inertia::render('Admin/Dashboard'); })->name('admin.payment-providers');
    Route::get('/payment-methods', function() { return Inertia::render('Admin/Dashboard'); })->name('admin.payment-methods');
    Route::get('/transactions', function() { return Inertia::render('Admin/Dashboard'); })->name('admin.transactions');
    Route::get('/vouchers', function() { return Inertia::render('Admin/Dashboard'); })->name('admin.vouchers');
    
    // Add more admin routes as needed
});

require __DIR__.'/auth.php';
