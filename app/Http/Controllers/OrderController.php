<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Produk;
use App\Models\Voucher;
use App\Models\PayMethod;
use App\Models\Pembelian;
use App\Models\WebConfig;
use Illuminate\Http\Request;
use App\Models\FlashsaleItem;
use App\Models\ItemThumbnail;
use App\Models\FlashsaleEvent;
use App\Models\Pembayaran;
use App\Http\Controllers\Admin\CheckUsernameController;
use App\Http\Controllers\MoogoldController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
                // Cari angka dari nama_layanan
                preg_match('/(\d+)/', $service->nama_layanan, $matches);
                $quantity = isset($matches[1]) ? (int)$matches[1] : null;

                $thumbnail = null;
                if ($quantity !== null) {
                    $thumbnail = ItemThumbnail::findThumbnailForQuantity($service->produk_id, $quantity);
                }

                // Kalau gagal dapet thumbnail dari quantity, fallback ke default
                if (!$thumbnail) {
                    $thumbnail = ItemThumbnail::where('produk_id', $service->produk_id)
                        ->default()
                        ->first();
                }

                return array_merge($service->toArray(), [
                    'thumbnail' => $thumbnail?->image_url
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

    /**
     * Check username for game account before processing order
     */
    public function checkUsername(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'game' => 'required|string',
            'user_id' => 'required|string',
            'zone_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid input data',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $checkUsernameController = new CheckUsernameController();
            return $checkUsernameController->getAccountUsername($request->all());
        } catch (\Exception $e) {
            Log::error('Username check failed', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to verify account username. Please try again.'
            ], 500);
        }
    }

    /**
     * Process order - Phase 1: Validation and Confirmation
     */
    public function validateOrder(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'layanan_id' => 'required|exists:layanans,id',
            'input_id' => 'required|string',
            'input_zone' => 'nullable|string',
            'payment_method' => 'required|string',
            'voucher_code' => 'nullable|string|exists:vouchers,code',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid input data',
                'errors' => $validator->errors()
            ], 422);
        }

        // Retrieve service information
        $layanan = \App\Models\Layanan::with('produk')->findOrFail($request->layanan_id);
        $produk = $layanan->produk;
        
        // Generate order ID
        $orderId = Pembelian::generateReferenceId();
        
        // Calculate price with discounts (flashsale, voucher)
        $price = $layanan->harga_jual;
        $originalPrice = $price;
        $discount = 0;
        $discountSource = null;
        
        // Check for active flashsale
        $flashsaleItem = $layanan->getActiveFlashsaleItem();
        if ($flashsaleItem) {
            $price = $flashsaleItem->harga_flashsale;
            $discount = $originalPrice - $price;
            $discountSource = 'flashsale';
        }
        
        // Check voucher if provided and not already using flashsale
        if ($request->voucher_code && !$flashsaleItem) {
            $voucher = Voucher::where('code', $request->voucher_code)
                ->where('status', 'active')
                ->where(function($query) {
                    $query->whereNull('end_date')
                          ->orWhere('end_date', '>', now());
                })
                ->first();
                
            if ($voucher) {
                // Validate voucher eligibility
                if ($price >= $voucher->min_purchase) {
                    $voucherDiscount = 0;
                    
                    if ($voucher->discount_type === 'percent') {
                        $voucherDiscount = $price * ($voucher->discount_value / 100);
                        if ($voucher->max_discount && $voucherDiscount > $voucher->max_discount) {
                            $voucherDiscount = $voucher->max_discount;
                        }
                    } else {
                        $voucherDiscount = $voucher->discount_value;
                    }
                    
                    $price -= $voucherDiscount;
                    $discount += $voucherDiscount;
                    $discountSource = 'voucher';
                }
            }
        }
        
        // Validate profit margin
        if ($price <= $layanan->harga_beli_idr) {
            return response()->json([
                'status' => 'error',
                'message' => 'This service is currently unavailable. Please try again later.'
            ], 422);
        }
        
        // Format payment method info
        $paymentMethod = null;
        if ($request->payment_method === 'saldo') {
            $paymentMethod = [
                'type' => 'saldo',
                'name' => 'Account Balance',
                'fee' => 0,
            ];
            
            // Check if user has sufficient balance
            $user = Auth::user();
            if (!$user || $user->saldo < $price) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Insufficient account balance.'
                ], 422);
            }
        } else {
            // Get payment method details
            $method = PayMethod::where('id', $request->payment_method)
                ->orWhere('kode', $request->payment_method)
                ->first();
                
            if (!$method) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid payment method.'
                ], 422);
            }
            
            // Calculate fees
            $fee = 0;
            if ($method->fee_type === 'fixed' && $method->fee_fixed > 0) {
                $fee = $method->fee_fixed;
            } elseif ($method->fee_type === 'percent' && $method->fee_percent > 0) {
                $fee = $price * ($method->fee_percent / 100);
            } elseif ($method->fee_type === 'both') {
                $fee = $method->fee_fixed + ($price * ($method->fee_percent / 100));
            }
            
            $paymentMethod = [
                'id' => $method->id,
                'type' => $method->tipe,
                'name' => $method->nama,
                'fee' => $fee,
            ];
        }
        
        // Check if username verification is needed
        $username = null;
        if ($produk->validasi_id === 'ya') {
            try {
                $params = [
                    'game' => $produk->slug,
                    'user_id' => $request->input_id,
                ];
                
                if ($request->input_zone) {
                    $params['zone_id'] = $request->input_zone;
                }
                
                $checkUsernameController = new CheckUsernameController();
                $response = $checkUsernameController->getAccountUsername($params);
                
                // Extract username from response
                if ($response && $response->getStatusCode() === 200) {
                    $data = json_decode($response->getContent(), true);
                    $username = $data['username'] ?? null;
                }
            } catch (\Exception $e) {
                Log::warning('Username verification failed but continuing', [
                    'error' => $e->getMessage(),
                    'product' => $produk->id
                ]);
                // We'll continue without username verification
            }
        }
        
        // Store order data in session for phase 2
        session()->put('pending_order', [
            'order_id' => $orderId,
            'layanan_id' => $layanan->id,
            'produk_id' => $produk->id, 
            'input_id' => $request->input_id,
            'input_zone' => $request->input_zone,
            'price' => $price,
            'original_price' => $originalPrice,
            'discount' => $discount,
            'discount_source' => $discountSource,
            'payment_method' => $paymentMethod,
            'profit' => $price - $layanan->harga_beli_idr,
            'voucher_code' => $request->voucher_code,
            'timestamp' => now()->timestamp,
        ]);
        
        // Return data for confirmation modal
        return response()->json([
            'status' => 'success',
            'order_data' => [
                'order_id' => $orderId,
                'service_name' => $layanan->nama_layanan,
                'game_name' => $produk->nama,
                'input_id' => $request->input_id,
                'input_zone' => $request->input_zone,
                'username' => $username,
                'price' => $price,
                'original_price' => $originalPrice,
                'discount' => $discount,
                'payment_method' => $paymentMethod,
            ]
        ]);
    }

    /**
     * Process order - Phase 2: Final Processing and Payment
     */
    public function processOrder(Request $request)
    {
        // Retrieve pending order from session
        $pendingOrder = session()->get('pending_order');
        if (!$pendingOrder) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order validation required. Please try again.'
            ], 422);
        }
        
        // Check if session has expired (15 minute window)
        if (now()->timestamp - $pendingOrder['timestamp'] > 900) {
            session()->forget('pending_order');
            return response()->json([
                'status' => 'error',
                'message' => 'Order session has expired. Please try again.'
            ], 422);
        }
        
        try {
            // Begin transaction
            \DB::beginTransaction();
            
            // Create pembelian record
            $pembelian = new Pembelian();
            $pembelian->order_id = $pendingOrder['order_id'];
            $pembelian->reference_id = $pendingOrder['order_id'];
            $pembelian->order_type = 'game';
            $pembelian->user_id = Auth::id();
            $pembelian->layanan_id = $pendingOrder['layanan_id'];
            $pembelian->input_id = $pendingOrder['input_id'];
            $pembelian->input_zone = $pendingOrder['input_zone'] ?? null;
            $pembelian->price = $pendingOrder['price'];
            $pembelian->profit = $pendingOrder['profit'];
            $pembelian->status = 'pending';
            $pembelian->save();
            
            // Process payment based on method type
            if ($pendingOrder['payment_method']['type'] === 'saldo') {
                // Deduct from user balance
                $user = Auth::user();
                $user->saldo -= $pendingOrder['price'];
                $user->save();
                
                // Process order via API
                $moogold = new MoogoldController();
                $layanan = \App\Models\Layanan::find($pendingOrder['layanan_id']);
                
                $apiResult = $moogold->createTransaction([
                    'category_id' => $layanan->produk->kategori_id,
                    'order_id' => $pendingOrder['order_id'],
                    'service_id' => $layanan->service_id,
                    'quantity' => 1,
                    'user_id' => $pendingOrder['input_id'],
                    'server' => $pendingOrder['input_zone'] ?? null
                ]);
                
                // Update order status based on API response
                if ($apiResult && isset($apiResult['data']) && $apiResult['data']['status'] === 'success') {
                    $pembelian->status = 'processing';
                    $pembelian->callback_data = $apiResult['data'];
                    $pembelian->save();
                    
                    \DB::commit();
                    
                    // Clear pending order
                    session()->forget('pending_order');
                    
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Order has been processed successfully.',
                        'redirect' => route('dashboard.transactions')
                    ]);
                } else {
                    // Failed API call, rollback transaction
                    \DB::rollBack();
                    
                    Log::error('API order failed', [
                        'order_id' => $pendingOrder['order_id'],
                        'response' => $apiResult ?? null
                    ]);
                    
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Order processing failed. Your balance has not been deducted.'
                    ], 500);
                }
            } else {
                // Create payment record for gateway payment
                $pembayaran = new Pembayaran();
                $pembayaran->order_id = $pendingOrder['order_id'];
                $pembayaran->price = $pendingOrder['price'] + ($pendingOrder['payment_method']['fee'] ?? 0);
                $pembayaran->payment_method = $pendingOrder['payment_method']['name'];
                $pembayaran->status = 'pending';
                $pembayaran->save();
                
                // Clear pending order
                session()->forget('pending_order');
                
                // Commit transaction
                \DB::commit();
                
                // TODO: Implement payment gateway integration here
                // For now, we'll just return a success response with a redirection
                return response()->json([
                    'status' => 'success',
                    'message' => 'Your order has been created. Please complete the payment.',
                    'redirect' => route('dashboard.transactions'),
                    'order_id' => $pendingOrder['order_id']
                ]);
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            
            Log::error('Order processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'order' => $pendingOrder
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing your order. Please try again.'
            ], 500);
        }
    }

    /**
     * Validate voucher code
     */
    public function validateVoucher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'layanan_id' => 'required|exists:layanans,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $voucher = Voucher::where('code', $request->code)
            ->where('status', 'active')
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>', now());
            })
            ->first();

        if (!$voucher) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired voucher code'
            ], 404);
        }

        // Check if the voucher has usage limits
        if ($voucher->usage_limit && $voucher->usage_count >= $voucher->usage_limit) {
            return response()->json([
                'status' => 'error',
                'message' => 'This voucher has reached its usage limit'
            ], 422);
        }

        // Get service price
        $layanan = \App\Models\Layanan::findOrFail($request->layanan_id);
        $price = $layanan->harga_jual;

        // Check minimum purchase amount
        if ($voucher->min_purchase > $price) {
            return response()->json([
                'status' => 'error',
                'message' => 'This voucher requires a minimum purchase of ' . number_format($voucher->min_purchase)
            ], 422);
        }

        // Calculate discount
        $discountAmount = 0;
        if ($voucher->discount_type === 'percent') {
            $discountAmount = $price * ($voucher->discount_value / 100);
            if ($voucher->max_discount && $discountAmount > $voucher->max_discount) {
                $discountAmount = $voucher->max_discount;
            }
        } else {
            $discountAmount = $voucher->discount_value;
        }

        $finalPrice = $price - $discountAmount;

        return response()->json([
            'status' => 'success',
            'voucher' => [
                'code' => $voucher->code,
                'discount_type' => $voucher->discount_type,
                'discount_value' => $voucher->discount_value,
                'discount_amount' => $discountAmount,
                'original_price' => $price,
                'final_price' => $finalPrice
            ]
        ]);
    }

    protected function generateUniqueOrderId()
    {
        do {
            $prefix = 'N'; // bisa diganti "M", "X", dll
            $timestamp = now()->format('dmHis'); // d=day, m=month, H=hour, i=minute, s=second
            $random = mt_rand(100, 999); // random 3 digit
            $orderId = $prefix . $timestamp . $random;
        } while ($this->orderIdExists($orderId));

        return $orderId;
    }

    protected function orderIdExists($orderId)
    {
        return Pembelian::where('order_id', $orderId)->exists();
    }
}
