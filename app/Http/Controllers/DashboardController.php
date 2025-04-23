<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Will pass dashboard overview data here in the future
        return Inertia::render('Dashboard/Index');
    }

    public function balance()
    {
        // Will pass balance data here in the future
        return Inertia::render('Dashboard/Balance');
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
