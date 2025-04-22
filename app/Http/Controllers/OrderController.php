
<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Models\Produk;

class OrderController extends Controller
{
    public function index($slug = null)
    {
        // Fetch the product based on slug
        $produk = null;
        if ($slug) {
            $produk = Produk::where('slug', $slug)->first();
            if (!$produk) {
                abort(404);
            }
        }

        // Get active vouchers
        $activeVouchers = Voucher::where('is_public', true)
            ->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>', now());
            })
            ->get()
            ->map(function($voucher) {
                return [
                    'code' => $voucher->code,
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

        // Example FAQs - In production, these would likely come from a database
        $faqs = [
            [
                'question' => 'How long do top-ups take?',
                'answer' => 'Instant delivery for 98% of orders. Some game servers may take up to 5 minutes to process.',
                'category' => 'delivery'
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept credit cards, e-wallets, bank transfers, and various local payment options.',
                'category' => 'payment'
            ],
            [
                'question' => 'Can I get a refund if there\'s an issue?',
                'answer' => 'Yes, we offer full refunds if there are any issues with your order that are within our control.',
                'category' => 'refunds'
            ],
            [
                'question' => 'Do I need to create an account to make a purchase?',
                'answer' => 'No, you can check out as a guest, but creating an account offers benefits like order tracking and special promotions.',
                'category' => 'account'
            ],
            [
                'question' => 'Are there any additional fees?',
                'answer' => 'All prices shown include taxes and fees. There are no hidden charges.',
                'category' => 'payment'
            ]
        ];

        // Current order total - Replace with actual calculation from your cart
        $currentTotal = 100000; // Example value

        return Inertia::render('Order/Index', [
            'title' => 'Place Order',
            'produk' => $produk,
            'activeVouchers' => $activeVouchers,
            'faqs' => $faqs,
            'currentTotal' => $currentTotal,
        ]);
    }

    public function applyVoucher(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = $request->input('code');

        // Find the voucher
        $voucher = Voucher::where('code', $code)
            ->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>', now());
            })
            ->first();

        if (!$voucher) {
            return back()->withErrors([
                'code' => 'Invalid voucher code',
            ]);
        }

        // Check usage limit
        if ($voucher->usage_limit && $voucher->usage_count >= $voucher->usage_limit) {
            return back()->withErrors([
                'code' => 'This voucher has reached its usage limit',
            ]);
        }

        // Check minimum purchase
        $currentTotal = 100000; // Replace with actual calculation from your cart
        if ($voucher->min_purchase && $currentTotal < $voucher->min_purchase) {
            return back()->withErrors([
                'code' => 'Minimum purchase of Rp ' . number_format($voucher->min_purchase) . ' required',
            ]);
        }

        // Apply the voucher (store in session)
        session()->put('applied_voucher', $voucher->code);

        return back()->with('status', [
            'type' => 'success',
            'action' => 'Success',
            'text' => 'Voucher applied successfully!'
        ]);
    }

    public function removeVoucher()
    {
        session()->forget('applied_voucher');

        return back()->with('status', [
            'type' => 'success',
            'action' => 'Success',
            'text' => 'Voucher removed successfully!'
        ]);
    }
}
