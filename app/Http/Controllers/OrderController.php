
<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\WebConfig;
use App\Models\FlashsaleItem;
use App\Models\ItemThumbnail;
use Inertia\Inertia;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Produk $produk)
    {
        $inputFields = $produk->inputFields()->with('options')->orderBy('order')->get();
        $waNumber = WebConfig::get('support_whatsapp', '');

        // Normal services (excluding flashsale items)
        $services = $produk->layanan()
            ->whereNotIn('id', function($query) {
                $query->select('layanan_id')
                    ->from('flashsale_items')
                    ->whereHas('flashsaleEvent', function($q) {
                        $q->where('status', 'active')
                            ->where('event_start_date', '<=', now())
                            ->where('event_end_date', '>=', now());
                    })
                    ->where('status', 'active');
            })
            ->get()
            ->map(function($service) {
                // Extract quantity from name (e.g., "150 UC" → 150)
                preg_match('/(\d+)/', $service->nama_layanan, $matches);
                $quantity = $matches[1] ?? null;
                
                return array_merge($service->toArray(), [
                    'thumbnail' => $service->gambar 
                        ?: ItemThumbnail::findThumbnailForQuantity(
                            $service->produk_id, 
                            $quantity
                        )?->image_url
                ]);
            });

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

        // Active flashsale items with stock/price validation
        $flashsaleItems = FlashsaleItem::with('flashsaleEvent', 'layanan')
            ->whereHas('layanan', function($q) use ($produk) {
                $q->where('produk_id', $produk->id);
            })
            ->whereHas('flashsaleEvent', function($q) {
                $q->where('status', 'active')
                    ->where('event_start_date', '<=', now())
                    ->where('event_end_date', '>=', now());
            })
            ->where('status', 'active')
            ->where(function($q) {
                $q->where('stok_tersedia', '>', 0)
                    ->orWhereNull('stok_tersedia');
            })
            ->whereHas('layanan', function($q) {
                $q->whereColumn('harga_beli_idr', '>', 'flashsale_items.harga_flashsale');
            })
            ->get()
            ->map(function($item) {
                $service = $item->layanan;
                // Similar thumbnail logic for flashsale items
                preg_match('/(\d+)/', $service->nama_layanan, $matches);
                $quantity = $matches[1] ?? null;
                
                $serviceWithThumbnail = array_merge($service->toArray(), [
                    'thumbnail' => $service->gambar 
                        ?: ItemThumbnail::findThumbnailForQuantity(
                            $service->produk_id, 
                            $quantity
                        )?->image_url,
                    'flashSaleItem' => $item
                ]);
                
                return $serviceWithThumbnail;
            });

        return Inertia::render('Order/Index', [
            'user' => auth()->user(),
            'produk' => $produk,
            'layanans' => $services,
            'flashsaleItems' => $flashsaleItems,
            'inputFields' => $inputFields,
            'waNumber' => $waNumber,
            'flashsaleEvents' => $flashsaleEvents
        ]);
    }
}
