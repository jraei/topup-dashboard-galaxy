<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Will pass dashboard overview data here in the future
        return Inertia::render('Dashboard/Index');
    }

    /**
     * Display the balance dashboard with filtered deposit history.
     */
    public function balance(Request $request)
    {
        $user = $request->user();
        // Smart paginator: page size from query or default to 15
        $perPage = $request->input('per_page', 15);

        return Inertia::render('Dashboard/Balance', [
            'balance' => $user->saldo,
            'deposits' => Deposit::forUser($user->id)
                ->withFilters($request)
                ->paginate($perPage)
                ->withQueryString(),
        ]);
    }

    /**
     * Show topup/payment method selection. Data scaffolding, full logic next step.
     */
    public function topup(Request $request)
    {
        $user = $request->user();
        return Inertia::render('Dashboard/Topup', [
            'balance' => $user->saldo,
            // You will fill payment methods here in next step
        ]);
    }

    public function transactions()
    {
        // Will pass transactions data here in the future
        return Inertia::render('Dashboard/Transactions');
    }

    public function mutations()
    {
        // Will pass mutation history here in the future
        return Inertia::render('Dashboard/Mutations');
    }

    public function affiliate()
    {
        // Will pass affiliate data here in the future
        return Inertia::render('Dashboard/Affiliate');
    }
}
