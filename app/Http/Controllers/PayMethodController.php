<?php

namespace App\Http\Controllers;

use App\Models\PayMethod;
use Illuminate\Http\Request;
use App\Http\Controllers\TripayController;

class PayMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.payMethod.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PayMethod $payMethod)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PayMethod $payMethod)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PayMethod $payMethod)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PayMethod $payMethod)
    {
        //
    }

    public function getMethod()
    {
        $tripay = new TripayController();
        $tripay->getTripayMethod();
        return back()->with('success', 'Berhasil menambahkan payment method!');
    }
}
