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

        // Get user role ID for price calculations
        $userRoleId = auth()->check() ? auth()->user()->user_role_id : null;

        // Fetch active flash sale events with optimized query
        $activeEvents = FlashsaleEvent::where('status', 'active')
            ->where('event_start_date', '<=', now())
            ->where('event_end_date', '>=', now())
            ->with(['item' => function ($query) use ($userRoleId) {
                $query->where('status', 'active')
                    ->where(function ($q) {
                        $q->where('stok_tersedia', '>', 0)
                            ->orWhereNull('stok_tersedia');
                    })
                    ->with(['layanan' => function ($q) use ($userRoleId) {
                        $q->where('status', 'active');
                        $q->with(['produk', 'provider']);
                    }]);
            }])
            ->first();

        return Inertia::render('User/Index', [
            'banners' => $banners,
            'flashsaleEvent' => $activeEvents,
            'serverTime' => Carbon::now()->toISOString(),
            'userRoleId' => $userRoleId,
        ]);
    }
}
