<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Inertia\Inertia;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Provider;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Gonon\Digiflazz\Digiflazz;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\DigiflazzController;

class ProdukController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'asc');

        // Validate the sort field to prevent SQL injection
        $allowedSortFields = ['nama', 'brand', 'kategori_id', 'provider_id', 'validasi_id', 'status'];
        if (!in_array($sort, $allowedSortFields)) {
            $sort = 'id';
        }

        // Start query
        $query = Produk::query();

        // Apply search if provided
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('developer', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('validasi_id', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('kategori', function ($q) use ($search) {
                        $q->where('kategori_name', 'like', "%{$search}%");
                    })->orWhereHas('provider', function ($q) use ($search) {
                        $q->where('provider_name', 'like', "%{$search}%");
                    });
            });
        }

        // Apply sorting
        $query->orderBy($sort, $direction);

        // Get paginated results
        $products = $query->paginate(5)->withQueryString();

        return Inertia::render('Admin/Products', [
            'products' => $products,
            'kategori_list' => Kategori::where('status', 'active')->get(),
            'provider_list' => Provider::all(),
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction
            ]
        ]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $product = $request->validate([
            'nama' => 'required|min:5',
            'developer' => 'required',
            'brand' => 'required',
            'kategori_id' => 'required|numeric',
            'slug' => 'required|unique:produks,slug',
            'provider_id' => 'required|numeric',
            'validasi_id' => 'required',
            'deskripsi_game' => 'required',
            'petunjuk_field' => 'image|mimes:jpeg,png,jpg,webp|max:10240',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'status' => 'required',
        ]);

        if ($request->hasFile('petunjuk_field')) {
            $product['petunjuk_field'] = $request->file('petunjuk_field')->store('produk/petunjuk-field', 'public');
        }
        if ($request->hasFile('thumbnail')) {
            $product['thumbnail'] = $request->file('thumbnail')->store('produk/thumbnail', 'public');
        }

        Produk::create($product);

        // return to_route('categories.index')->with('success', 'New Category has been added!');
        return to_route('products.index')->with('status', ['type' => 'success', 'action' => 'Success', 'text' => 'New Product has been added!']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Produk::with('kategori', 'provider')->where('id', $id)->first();
        return response()->json([
            'product' => $product
        ]);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);
        // update data berdasarkan id
        $rules = [
            'nama' => 'required|min:5',
            'developer' => 'required',
            'brand' => 'required',
            'kategori_id' => 'required|numeric',
            'provider_id' => 'required|numeric',
            'validasi_id' => 'required',
            'deskripsi_game' => 'required',
            'status' => 'required',
        ];


        // Jika ada file baru, tambahkan validasi gambar
        if ($request->hasFile('petunjuk_field')) {
            $rules['petunjuk_field'] = 'image|mimes:jpeg,png,jpg,webp|max:10240';
        }

        if ($request->hasFile('thumbnail')) {
            $rules['thumbnail'] = 'image|mimes:jpeg,png,jpg,webp|max:10240';
        }
        // Cek jika slug berubah

        if ($request->slug != $produk->slug) {
            $rules['slug'] = 'required|unique:produks,slug';
        }

        // validasi data
        $validatedData = $request->validate($rules);

        // Simpan file jika ada yang diunggah
        if ($request->hasFile('petunjuk_field')) {
            if ($produk->petunjuk_field) {
                Storage::delete($produk->petunjuk_field);
            }
            $validatedData['petunjuk_field'] = $request->file('petunjuk_field')->store('produk/petunjuk-field', 'public');
        }
        if ($request->hasFile('thumbnail')) {
            if ($produk->thumbnail) {
                Storage::disk('public')->delete($produk->thumbnail);
            }
            $validatedData['thumbnail'] = $request->file('thumbnail')->store('produk/thumbnail', 'public');
        }

        // Update data produk

        $produk->update($validatedData);

        return to_route('products.index')->with('status', ['type' => 'success', 'action' => 'Success', 'text' => 'Product has been updated!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // hapus data berdasarkan id
        // dd($product->id);

        $product = Produk::find($id);
        if ($product->petunjuk_field) {
            Storage::disk('public')->delete($product->petunjuk_field);
        }
        if ($product->thumbnail) {
            Storage::disk('public')->delete($product->thumbnail);
        }
        $product->delete();

        return to_route('products.index')->with('status', ['type' => 'success', 'action' => 'Success', 'text' => 'Product has been deleted!']);
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
