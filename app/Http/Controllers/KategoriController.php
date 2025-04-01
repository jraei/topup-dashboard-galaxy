<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Gonon\Digiflazz\Digiflazz;


class KategoriController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kategori = Kategori::paginate(5);
        return Inertia::render('Admin/Categories', [
            'categories' => $kategori
        ]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $kategori = $request->validate([
            'kategori_name' => 'required|unique:kategoris,kategori_name|max:50',
            'status' => 'required'
        ]);

        Kategori::create($kategori);

        // return to_route('categories.index')->with('success', 'New Category has been added!');
        return to_route('categories.index')->with('status', ['type' => 'success', 'action' => 'Success', 'text' => 'New Category has been added!']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $kategori = Kategori::find($id);
        $kategori->product_count = $kategori->produk()->count();
        return response()->json([
            'category' => $kategori
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kategori $kategori)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // update data berdasarkan id
        $validatedData = $request->validate([
            'kategori_name' => 'required|unique:kategoris,kategori_name|max:50',
            'status' => 'required'
        ]);

        Kategori::where('id', $id)->update($validatedData);

        return to_route('categories.index')->with('status', ['type' => 'success', 'action' => 'Success', 'text' => 'Category has been updated!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // hapus data berdasarkan id
        // dd($kategori->id);
        $kategori = Kategori::find($id);
        $kategori->delete();

        return to_route('categories.index')->with('status', ['type' => 'success', 'action' => 'Success', 'text' => 'Category has been deleted!']);
    }
}
