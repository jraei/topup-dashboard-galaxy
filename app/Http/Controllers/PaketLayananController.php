<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Produk;
use App\Models\Layanan;
use App\Models\PaketLayanan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PaketLayananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'asc');
        $productFilter = $request->input('product');

        // Validate the sort field to prevent SQL injection
        $allowedSortFields = ['id', 'judul_paket', 'created_at'];
        if (!in_array($sort, $allowedSortFields)) {
            $sort = 'id';
        }

        // Start query with relationships and services count
        $query = PaketLayanan::with(['produk'])
            ->withCount('layanans');

        // Apply filters
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('judul_paket', 'like', "%{$search}%")
                  ->orWhere('informasi', 'like', "%{$search}%")
                  ->orWhereHas('produk', function ($subQ) use ($search) {
                      $subQ->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        if ($productFilter) {
            $query->where('produk_id', $productFilter);
        }

        // Apply sorting
        $query->orderBy($sort, $direction);

        // Get paginated results
        $paketLayanans = $query->paginate(10)->withQueryString();

        // Get active products and services for form
        $products = Produk::where('status', 'active')->get();
        $services = Layanan::where('status', 'active')
            ->with(['produk'])
            ->whereNull('paket_layanan_id')
            ->get();

        return Inertia::render('Admin/PaketLayanan', [
            'paketLayanans' => $paketLayanans,
            'products' => $products,
            'services' => $services,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction,
                'product' => $productFilter,
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul_paket' => 'required|string|max:255',
            'produk_id' => 'nullable|exists:produks,id',
            'informasi' => 'nullable|string',
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'exists:layanans,id'
        ]);

        DB::beginTransaction();
        try {
            // Create the package
            $paketLayanan = PaketLayanan::create([
                'judul_paket' => $validated['judul_paket'],
                'produk_id' => $validated['produk_id'],
                'informasi' => $validated['informasi'],
            ]);

            // Update selected services to link to this package
            Layanan::whereIn('id', $validated['service_ids'])
                ->update(['paket_layanan_id' => $paketLayanan->id]);

            DB::commit();

            return to_route('paket-layanans.index')->with('status', [
                'type' => 'success',
                'action' => 'Success',
                'text' => 'Service Package has been created successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('status', [
                'type' => 'error',
                'action' => 'Error',
                'text' => 'Failed to create service package: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $paketLayanan = PaketLayanan::with(['produk', 'layanans.produk'])->find($id);

        return response()->json([
            'paketLayanan' => $paketLayanan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'judul_paket' => 'required|string|max:255',
            'produk_id' => 'nullable|exists:produks,id',
            'informasi' => 'nullable|string',
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'exists:layanans,id'
        ]);

        DB::beginTransaction();
        try {
            $paketLayanan = PaketLayanan::findOrFail($id);
            
            // Update the package
            $paketLayanan->update([
                'judul_paket' => $validated['judul_paket'],
                'produk_id' => $validated['produk_id'],
                'informasi' => $validated['informasi'],
            ]);

            // Remove old service associations
            Layanan::where('paket_layanan_id', $paketLayanan->id)
                ->update(['paket_layanan_id' => null]);

            // Update selected services to link to this package
            Layanan::whereIn('id', $validated['service_ids'])
                ->update(['paket_layanan_id' => $paketLayanan->id]);

            DB::commit();

            return to_route('paket-layanans.index')->with('status', [
                'type' => 'success',
                'action' => 'Success',
                'text' => 'Service Package has been updated successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('status', [
                'type' => 'error',
                'action' => 'Error',
                'text' => 'Failed to update service package: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $paketLayanan = PaketLayanan::findOrFail($id);
            
            // Remove service associations
            Layanan::where('paket_layanan_id', $paketLayanan->id)
                ->update(['paket_layanan_id' => null]);

            // Delete the package
            $paketLayanan->delete();

            DB::commit();

            return to_route('paket-layanans.index')->with('status', [
                'type' => 'success',
                'action' => 'Success',
                'text' => 'Service Package has been deleted successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('status', [
                'type' => 'error',
                'action' => 'Error',
                'text' => 'Failed to delete service package: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get available services for a specific product or all services
     */
    public function getAvailableServices(Request $request)
    {
        $productId = $request->input('product_id');
        $excludePackageId = $request->input('exclude_package_id');

        $query = Layanan::where('status', 'active')
            ->with(['produk'])
            ->where(function($q) use ($excludePackageId) {
                $q->whereNull('paket_layanan_id');
                if ($excludePackageId) {
                    $q->orWhere('paket_layanan_id', $excludePackageId);
                }
            });

        if ($productId) {
            $query->where('produk_id', $productId);
        }

        $services = $query->get();

        return response()->json([
            'services' => $services
        ]);
    }

    /**
     * Get services assigned to a specific package
     */
    public function getPackageServices($id)
    {
        $services = Layanan::where('paket_layanan_id', $id)
            ->where('status', 'active')
            ->with(['produk'])
            ->get();

        return response()->json([
            'services' => $services
        ]);
    }
}
