<?php

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\Admin\CheckUsernameController;
use App\Http\Controllers\Admin\DigiflazzController;
use App\Http\Controllers\Admin\TripayCallbackController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('api.key')->group(function () {
    Route::post('/balance', [ApiController::class, 'balance']);
    Route::post('/products', [ApiController::class, 'products']);
    Route::post('/services', [ApiController::class, 'services']);
    Route::post('/order', [ApiController::class, 'order']);
    Route::post('/order/status', [ApiController::class, 'checkOrderStatus']);
});

Route::post('/h2h/digiflazz/order', [DigiflazzController::class, 'handleOrder'])->name('h2h.digiflazz.order');

// Product search endpoint
Route::get('/search/products', function (Request $request) {
    $query = $request->input('query');

    if (empty($query) || strlen($query) < 2) {
        return response()->json([]);
    }

    $products = Produk::where('nama', 'LIKE', "%{$query}%")
        ->where('status', 'active')
        ->take(5)
        ->get(['id', 'nama', 'thumbnail', 'slug']);

    return response()->json($products);
})->name('api.search.products');

// Calculator endpoints
// Route::post('/calculator/winrate', [CalculatorController::class, 'calculateWinrate']);

// payment gateway callback handle
Route::prefix('callback')->group(function () {
    Route::post('/tripay', [TripayCallbackController::class, 'handle']);
});
