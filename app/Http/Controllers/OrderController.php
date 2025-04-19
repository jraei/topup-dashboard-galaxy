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

        // Get active flashsale events related to this product
        $flashsaleEvents = $produk->layanan()
            ->whereHas('flashSaleItem.flashsaleEvent', function ($q) {
                $q->where('status', 'active')
                    ->where('event_start_date', '<=', now())
                    ->where('event_end_date', '>=', now());
            })
            ->with(['flashSaleItem.flashsaleEvent'])
            ->get()
            ->pluck('flashSaleItem.flashsaleEvent')
            ->unique('id');

        return Inertia::render('Order/Index', [
            'user' => auth()->user(),
            'produk' => $produk,
            'layanans' => $layanans,
            'inputFields' => $inputFields,
            'waNumber' => $waNumber,
            'flashsaleEvents' => $flashsaleEvents
        ]);
    }
}