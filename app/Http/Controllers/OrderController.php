<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Produk;
use App\Models\PayMethod;
use App\Models\Voucher;
use App\Models\WebConfig;
use Illuminate\Http\Request;
use App\Models\FlashsaleItem;
use App\Models\ItemThumbnail;
use App\Models\FlashsaleEvent;

class OrderController extends Controller
{
    public function index(Produk $produk)
    {
        $inputFields = $produk->inputFields()->with('options')->orderBy('order')->get();
        $waNumber = WebConfig::get('support_whatsapp', '');

        $excludedLayananIds = FlashsaleItem::whereHas('flashsaleEvent', function ($q) {
            $q->where('status', 'active')
                ->where('event_start_date', '<=', now())
                ->where('event_end_date', '>=', now());
        })
            ->where('status', 'active')
            ->pluck('layanan_id');

        // Normal services (excluding flashsale items)
        $services = $produk->layanan()
            ->whereNotIn('id', $excludedLayananIds)
            ->orderBy('harga_beli_idr', 'asc')
            ->get()
            ->map(function ($service) {
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
        $flashsaleEvents = FlashsaleEvent::whereHas('layanan', function ($q) use ($produk) {
            $q->where('produk_id', $produk->id);
        })
            ->where('status', 'active')
            ->where('event_start_date', '<=', now())
            ->where('event_end_date', '>=', now())
            ->get();

        // Active flashsale items with stock/price validation
        $flashsaleItems = FlashsaleItem::with('flashsaleEvent', 'layanan')
            ->whereHas('layanan', function ($q) use ($produk) {
                $q->where('produk_id', $produk->id);
            })
            ->whereHas('flashsaleEvent', function ($q) {
                $q->where('status', 'active')
                    ->where('event_start_date', '<=', now())
                    ->where('event_end_date', '>=', now());
            })
            ->where('status', 'active')
            ->where(function ($q) {
                $q->where('stok_tersedia', '>', 0)
                    ->orWhereNull('stok_tersedia');
            })
            ->orderBy('harga_flashsale', 'asc')
            ->get()
            ->filter(function ($item) {
                $layanan = $item->layanan;
                return $layanan->harga_jual > $item->harga_flashsale;
            })
            ->map(function ($item) {
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
                    'flashSaleItem' => array_merge($item->toArray(), [
                        'is_active' => true, // Add boolean flag for frontend use
                    ])
                ]);

                return $serviceWithThumbnail;
            });

        // Payment Method Data Assembly ---
        // Static methods (saldo, qris)
        $staticMethods = [
            'saldo' => PayMethod::where('tipe', 'Saldo Akun')->first(),
            'qris' => [
                'nama' => 'QRIS (Semua Pembayaran)',
                'gambar' => PayMethod::where('tipe', 'QRIS')->first()?->gambar,
                'fee_fixed' => PayMethod::where('tipe', 'QRIS')->first()?->fee_fixed,
                'fee_percent' => PayMethod::where('tipe', 'QRIS')->first()?->fee_percent,
                'fee_type' => PayMethod::where('tipe', 'QRIS')->first()?->fee_type,
            ]
        ];
        // Dynamic methods (grouped by tipe)
        $dynamicMethods = PayMethod::whereNotIn('tipe', ['saldo', 'QRIS'])
            ->where('status', 'active')
            ->with('paymentProvider')
            ->get()
            ->groupBy('tipe')
            ->map(function ($group) {
                return $group->map(function ($method) {
                    return [
                        'id' => $method->id,
                        'nama' => $method->nama,
                        'tipe' => $method->tipe,
                        'fee_fixed' => $method->fee_fixed,
                        'fee_percent' => $method->fee_percent,
                        'fee_type' => $method->fee_type,
                        'gambar' => $method->gambar,
                        'is_recommended' => $method->keterangan && str_contains(strtolower($method->keterangan), 'recommended'),
                        'payment_provider' => $method->paymentProvider?->toArray(),
                    ];
                })->values();
            });

        // Get active vouchers for public display
        $activeVouchers = Voucher::where('is_public', true)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>', now());
            })
            ->get()
            ->map(function ($voucher) {
                return [
                    'code' => $voucher->code,
                    'description' => $voucher->description,
                    'discount_value' => $voucher->discount_value,
                    'discount_type' => $voucher->discount_type,
                    'end_date' => $voucher->end_date?->format('d M Y'),
                    'max_discount' => $voucher->max_discount,
                    'min_purchase' => $voucher->min_purchase,
                    'usage_limit' => $voucher->usage_limit,
                    'usage_count' => $voucher->usage_count,
                    'is_public' => $voucher->is_public
                ];
            });

        // FAQs for product
        $faqs = [
            [
                'question' => 'How long does top-up take?',
                'answer' => 'Instant delivery for 90% of orders. Most top-ups are processed automatically and delivered within seconds. For manual processing, it may take up to 5 minutes during peak hours.',
                'category' => 'delivery'
            ],
            [
                'question' => 'Is it safe to purchase here?',
                'answer' => 'Yes, all transactions are secured with industry-standard encryption. We never store your complete payment details and have been serving customers since 2020 with a 99.7% satisfaction rate.',
                'category' => 'security'
            ],
            [
                'question' => 'What payment methods are accepted?',
                'answer' => 'We accept a wide range of payment methods including credit/debit cards, e-wallets, bank transfers, and convenience store payments. You can view all available options during checkout.',
                'category' => 'payment'
            ],
            [
                'question' => 'Can I get a refund if there\'s a problem?',
                'answer' => 'We offer a full refund if the top-up fails to deliver. Please contact our support team within 24 hours of purchase with your order number for assistance.',
                'category' => 'refunds'
            ]
        ];

        return Inertia::render('Order/Index', [
            'user' => auth()->user(),
            'produk' => $produk,
            'layanans' => $services,
            'flashsaleItems' => $flashsaleItems,
            'inputFields' => $inputFields,
            'waNumber' => $waNumber,
            'flashsaleEvents' => $flashsaleEvents,
            'staticMethods' => $staticMethods,
            'dynamicMethods' => $dynamicMethods,
            'activeVouchers' => $activeVouchers,
            'faqs' => $faqs,
        ]);
    }
}
