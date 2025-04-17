
<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\WebConfig;
use Inertia\Inertia;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Produk $produk)
    {
        $layanans = $produk->layanan;
        $inputFields = $produk->inputFields()->with('options')->orderBy('order')->get();
        $waNumber = WebConfig::get('support_whatsapp', '');
        
        return Inertia::render('Order/Index', [
            'user' => auth()->user(),
            'produk' => $produk,
            'layanans' => $layanans,
            'inputFields' => $inputFields,
            'waNumber' => $waNumber
        ]);
    }
}
