<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Inertia\Inertia;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Produk $produk)
    {
        $layanans = $produk->layanan;
        $inputFields = $produk->inputFields()->with('options')->orderBy('order')->get();
        
        return Inertia::render('Order/Index', [
            'user' => auth()->user(),
            'produk' => $produk,
            'layanans' => $layanans,
            'inputFields' => $inputFields
        ]);
    }
}