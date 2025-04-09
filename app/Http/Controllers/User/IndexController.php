
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
        $userRoleId = auth()->check() ? auth()->user()->user_roles_id : null;

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
                        
                        // Include the role-specific price calculation data
                        $q->withCount(['flashSaleItem as regular_price' => function($price) use ($userRoleId) {
                            $price->selectRaw('COALESCE(
                                (SELECT layanans.harga_beli_idr FROM layanans 
                                WHERE layanans.id = flashsale_items.layanan_id),
                                0
                            )');
                        }]);
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
