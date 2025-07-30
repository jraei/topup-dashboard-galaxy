<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Produk;
use App\Models\Layanan;
use App\Models\PaketLayanan;
use App\Models\FusionService;
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
        $query = PaketLayanan::withCount(['layanans', 'fusionServices']);

        // Apply filters
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul_paket', 'like', "%{$search}%")
                    ->orWhere('informasi', 'like', "%{$search}%");
            });
        }

        // Apply sorting with display_order priority
        if ($sort === 'id') {
            $query->orderBy('display_order', 'asc')->orderBy('id', $direction);
        } else {
            $query->orderBy($sort, $direction);
        }

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
            'informasi' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'exists:layanans,id'
        ]);

        DB::beginTransaction();
        try {
            // Create the package
            $paketLayanan = PaketLayanan::create([
                'judul_paket' => $validated['judul_paket'],
                'informasi' => $validated['informasi'],
                'display_order' => $validated['display_order'] ?? 0,
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
        $paketLayanan = PaketLayanan::with(['layanans.produk', 'fusionServices'])->find($id);

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
            'informasi' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'exists:layanans,id'
        ]);

        DB::beginTransaction();
        try {
            $paketLayanan = PaketLayanan::findOrFail($id);

            // Update the package
            $paketLayanan->update([
                'judul_paket' => $validated['judul_paket'],
                'informasi' => $validated['informasi'],
                'display_order' => $validated['display_order'] ?? 0,
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
        $excludePackageId = $request->input('exclude_package_id');

        $query = Layanan::where('status', 'active')
            ->with(['produk'])
            ->where(function ($q) use ($excludePackageId) {
                $q->whereNull('paket_layanan_id');
                if ($excludePackageId) {
                    $q->orWhere('paket_layanan_id', $excludePackageId);
                }
            });

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

    /**
     * Store a new fusion service
     */
    public function storeFusionService(Request $request, $packageId)
    {
        $validated = $request->validate([
            'nama_fusion' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'layanan_ids' => 'required|array|min:2',
            'layanan_ids.*' => 'exists:layanans,id',
            'custom_price' => 'nullable|numeric|min:0',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $paketLayanan = PaketLayanan::findOrFail($packageId);

        $fusionService = FusionService::create([
            'nama_fusion' => $validated['nama_fusion'],
            'deskripsi' => $validated['deskripsi'],
            'paket_layanan_id' => $paketLayanan->id,
            'layanan_ids' => $validated['layanan_ids'],
            'custom_price' => $validated['custom_price'],
            'display_order' => $validated['display_order'] ?? 0,
        ]);

        return response()->json([
            'status' => 'success',
            'fusion_service' => $fusionService,
        ]);
    }

    /**
     * Update a fusion service
     */
    public function updateFusionService(Request $request, $packageId, $fusionId)
    {
        $validated = $request->validate([
            'nama_fusion' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'layanan_ids' => 'required|array|min:2',
            'layanan_ids.*' => 'exists:layanans,id',
            'custom_price' => 'nullable|numeric|min:0',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $fusionService = FusionService::where('paket_layanan_id', $packageId)
            ->findOrFail($fusionId);

        $fusionService->update([
            'nama_fusion' => $validated['nama_fusion'],
            'deskripsi' => $validated['deskripsi'],
            'layanan_ids' => $validated['layanan_ids'],
            'custom_price' => $validated['custom_price'],
            'display_order' => $validated['display_order'] ?? 0,
        ]);

        return response()->json([
            'status' => 'success',
            'fusion_service' => $fusionService,
        ]);
    }

    /**
     * Delete a fusion service
     */
    public function destroyFusionService($packageId, $fusionId)
    {
        $fusionService = FusionService::where('paket_layanan_id', $packageId)
            ->findOrFail($fusionId);

        $fusionService->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Fusion service deleted successfully',
        ]);
    }

    /**
     * Get fusion services for a package
     */
    public function getFusionServices($packageId)
    {
        $fusionServices = FusionService::where('paket_layanan_id', $packageId)
            ->orderBy('display_order')
            ->get()
            ->map(function ($fusion) {
                $services = $fusion->services();
                return [
                    'id' => $fusion->id,
                    'nama_fusion' => $fusion->nama_fusion,
                    'deskripsi' => $fusion->deskripsi,
                    'layanan_ids' => $fusion->layanan_ids,
                    'custom_price' => $fusion->custom_price,
                    'display_order' => $fusion->display_order,
                    'services' => $services,
                    'calculated_price' => $fusion->calculateTotalPrice(),
                ];
            });

        return response()->json([
            'fusion_services' => $fusionServices,
        ]);
    }
}
