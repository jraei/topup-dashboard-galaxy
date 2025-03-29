<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Gonon\Digiflazz\Digiflazz;
use App\Http\Controllers\DigiflazzController;
use Exception;

class ProdukController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.produk.index');
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
    public function show(Produk $produk)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produk $produk)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Produk $produk)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produk $produk)
    {
        //
    }


    public function getProduk()
    {
        try {
            $digiflazz = new DigiflazzController();
            $digiflazz->getDigiflazzProduk();
            return back()->with('success', 'Berhasil menambahkan produk!');
        } catch (Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }
}