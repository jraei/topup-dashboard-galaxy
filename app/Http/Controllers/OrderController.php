<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Inertia\Inertia;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Produk $produk)
    {
        return Inertia::render('Order/Index', [
            'produk' => $produk
        ]);
    }
}
