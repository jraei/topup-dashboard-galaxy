
<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Inertia\Inertia;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Produk $produk)
    {
        // Get layanan data
        $layanans = $produk->layanan;
        
        // Load input fields with their options
        $produk->load(['inputFields' => function($query) {
            $query->with('options')
                ->orderBy('order');
        }]);
        
        return Inertia::render('Order/Index', [
            'user' => auth()->user(),
            'produk' => $produk,
            'layanans' => $layanans
        ]);
    }
}
