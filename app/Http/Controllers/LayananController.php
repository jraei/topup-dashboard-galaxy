<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Kategori;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Gonon\Digiflazz\Digiflazz;
use App\Http\Controllers\DigiflazzController;

class LayananController extends Controller
{

    public function __construct()
    {
        Digiflazz::initDigiflazz(env('DIGIFLAZZ_USERNAME'), env('DIGIFLAZZ_APIKEY'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.layanan.index');
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
    public function show(Layanan $layanan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Layanan $layanan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Layanan $layanan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Layanan $layanan)
    {
        //
    }

    public function getService()
    {
        $digiflazz = new DigiflazzController();
        $digiflazz->getDigiflazzService();

        return back()->with('success', 'Berhasil Menambahkan Layanan!');
    }
}
