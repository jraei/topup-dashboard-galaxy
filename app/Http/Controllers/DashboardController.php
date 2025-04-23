
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Pembelian;
use App\Models\Pembayaran;
use App\Models\UserAffiliate;

class DashboardController extends Controller
{
    // Main metrics overview page
    public function index(Request $request)
    {
        $user = $request->user();

        // Example: status counts (to be optimized with correct eager loads/chunking)
        $statusCounts = $user->pembelian()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Last 7 days balance for graph
        $recentBalances = $user->pembayaran()
            ->orderBy('created_at', 'desc')
            ->take(7)
            ->pluck('price', 'created_at');

        // Recent 5 activities
        $recentActivities = $user->pembelian()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Affiliate data
        $affiliate = $user->affiliate()->with('referredUsers')->first();

        return Inertia::render('Dashboard/Dashboard', [
            'userData' => $user,
            'statusCounts' => $statusCounts,
            'recentBalances' => $recentBalances,
            'recentActivities' => $recentActivities,
            'affiliate' => $affiliate,
        ]);
    }

    // Transactions listing
    public function transactions(Request $request)
    {
        $user = $request->user();
        $transactions = $user->pembelian()
            ->orderBy('created_at', 'desc')
            ->with('layanan')
            ->paginate(15);

        return Inertia::render('Dashboard/Transactions', [
            'transactions' => $transactions,
        ]);
    }

    // Balance mutation listing
    public function mutations(Request $request)
    {
        $user = $request->user();
        $mutations = $user->pembayaran()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Dashboard/Mutations', [
            'mutations' => $mutations,
        ]);
    }

    // Affiliate/referral program overview
    public function affiliate(Request $request)
    {
        $user = $request->user();
        $affiliate = $user->affiliate()->with('referredUsers')->first();

        return Inertia::render('Dashboard/Affiliate', [
            'affiliate' => $affiliate,
        ]);
    }
}
