<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Deposit;

class DashboardController extends Controller
{
    public function index()
    {
        // Will pass dashboard overview data here in the future
        return Inertia::render('Dashboard/Index');
    }

    public function balance(Request $request)
    {
        $user = auth()->user();

        // Backend-powered datatable with filters/sort/search
        $deposits = Deposit::with(['pay_method'])
            ->withFilters($request)
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Dashboard/Balance', [
            'balance' => $user->saldo,
            'deposits' => $deposits,
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
