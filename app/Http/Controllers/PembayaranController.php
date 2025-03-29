
<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class PembayaranController extends Controller
{
    /**
     * Display a listing of the payments.
     */
    public function index()
    {
        $payments = Pembayaran::with('user')->latest()->paginate(10);
        
        return Inertia::render('Admin/Payments', [
            'payments' => $payments
        ]);
    }

    /**
     * Store a newly created payment in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1000',
            'payment_method' => 'required|string',
            'status' => 'required|in:pending,completed,failed',
            'description' => 'nullable|string|max:255',
            'transaction_id' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $payment = Pembayaran::create($validator->validated());

        return response()->json([
            'message' => 'Payment created successfully',
            'payment' => $payment
        ], 201);
    }

    /**
     * Display the specified payment.
     */
    public function show($id)
    {
        $payment = Pembayaran::with('user')->findOrFail($id);
        
        return response()->json(['payment' => $payment]);
    }

    /**
     * Update the specified payment in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'sometimes|numeric|min:1000',
            'payment_method' => 'sometimes|string',
            'status' => 'sometimes|in:pending,completed,failed',
            'description' => 'nullable|string|max:255',
            'transaction_id' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $payment = Pembayaran::findOrFail($id);
        $payment->update($validator->validated());

        return response()->json([
            'message' => 'Payment updated successfully',
            'payment' => $payment
        ]);
    }

    /**
     * Remove the specified payment from storage.
     */
    public function destroy($id)
    {
        $payment = Pembayaran::findOrFail($id);
        $payment->delete();
        
        return response()->json(['message' => 'Payment deleted successfully']);
    }

    /**
     * Process a payment with an external provider
     */
    public function processPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1000',
            'payment_method_id' => 'required|exists:pay_methods,id',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Here you would integrate with an actual payment gateway
        // This is a placeholder for the payment gateway API call
        
        // For demonstration, we'll create a dummy successful payment
        $payment = Pembayaran::create([
            'user_id' => $request->user_id,
            'amount' => $request->amount,
            'payment_method' => 'credit_card', // This would be dynamic based on payment_method_id
            'status' => 'completed',
            'description' => $request->description,
            'transaction_id' => 'TX-' . rand(10000, 99999),
        ]);

        return response()->json([
            'message' => 'Payment processed successfully',
            'payment' => $payment
        ], 201);
    }

    /**
     * Get payment statistics
     */
    public function statistics()
    {
        $totalPayments = Pembayaran::count();
        $totalSuccess = Pembayaran::where('status', 'completed')->count();
        $totalFailed = Pembayaran::where('status', 'failed')->count();
        $totalPending = Pembayaran::where('status', 'pending')->count();
        
        $totalAmount = Pembayaran::where('status', 'completed')->sum('amount');
        
        // Get recent payments
        $recentPayments = Pembayaran::with('user')
                                   ->latest()
                                   ->take(5)
                                   ->get();

        return response()->json([
            'total_payments' => $totalPayments,
            'total_success' => $totalSuccess,
            'total_failed' => $totalFailed,
            'total_pending' => $totalPending,
            'total_amount' => $totalAmount,
            'recent_payments' => $recentPayments
        ]);
    }
}
