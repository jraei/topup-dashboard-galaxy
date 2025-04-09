<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\FlashsaleEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IndexController extends Controller
{
    public function index()
    {
        $banners = Banner::where('status', 'active')
            ->orderBy('order')
            ->get(['id', 'image_path']);

        // Fetch active flash sale events
        $activeEvents = FlashsaleEvent::where('status', 'active')
            ->where('event_start_date', '<=', now())
            ->where('event_end_date', '>=', now())
            ->with(['item' => function ($query) {
                $query->where('status', 'active')
                    ->where(function ($q) {
                        $q->where('stok_tersedia', '>', 0)
                            ->orWhereNull('stok_tersedia');
                    })
                    ->with(['layanan' => function ($q) {
                        $q->where('status', 'active');
                        $q->with('produk');
                    }]);
            }])
            ->first();

        return Inertia::render('User/Index', [
            'banners' => $banners,
            'flashsaleEvent' => $activeEvents,
            'serverTime' => Carbon::now()->toISOString(),
        ]);
    }
}