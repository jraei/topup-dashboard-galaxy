<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Layanan;
use App\Models\UserRole;
use App\Models\PaketLayanan;
use App\Models\FusionService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FusionServiceController extends Controller
{
    /**
     * Display a listing of fusion services.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        $packageFilter = $request->input('package');
        $statusFilter = $request->input('status');

        // Validate the sort field to prevent SQL injection
        $allowedSortFields = ['id', 'nama_fusion', 'status', 'display_order', 'created_at'];
        if (!in_array($sort, $allowedSortFields)) {
            $sort = 'id';
        }

        // Start query
        $query = FusionService::with(['paketLayanan']);

        // Apply filters
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_fusion', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('paketLayanan', function ($subQ) use ($search) {
                      $subQ->where('judul_paket', 'like', "%{$search}%");
                  });
            });
        }

        if ($packageFilter) {
            $query->where('paket_layanan_id', $packageFilter);
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        // Apply sorting
        $query->orderBy($sort, $direction);

        // Get paginated results
        $fusionServices = $query->paginate(10)->withQueryString();

        // Add calculated price and service details to each fusion service
        $fusionServices->getCollection()->transform(function ($fusion) {
            $fusion->service_details = $fusion->services();
            $fusion->calculated_price = $fusion->calculateTotalPrice();
            $fusion->final_price = $fusion->calculateFinalPrice();
            $fusion->service_count = $fusion->service_details->count();
            
            return $fusion;
        });

        // Get active packages for form
        $packages = PaketLayanan::orderBy('judul_paket')->get();

        return Inertia::render('Admin/FusionServices', [
            'fusionServices' => $fusionServices,
            'packages' => $packages,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction,
                'package' => $packageFilter,
                'status' => $statusFilter
            ]
        ]);
    }

    /**
     * Get services for a specific package
     */
    public function getServicesForPackage(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:paket_layanans,id'
        ]);

        $services = Layanan::where('paket_layanan_id', $validated['package_id'])
            ->where('status', 'active')
            ->with(['produk.provider'])
            ->get();

        return response()->json([
            'success' => true,
            'services' => $services
        ]);
    }

    /**
     * Store a newly created fusion service.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_fusion' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'paket_layanan_id' => 'required|exists:paket_layanans,id',
            'layanan_ids' => 'required|array|min:2|max:5',
            'layanan_ids.*' => 'exists:layanans,id',
            'custom_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'display_order' => 'integer|min:0'
        ]);

        // Validate that all services belong to the selected package
        $servicesInPackage = Layanan::where('paket_layanan_id', $validated['paket_layanan_id'])
            ->whereIn('id', $validated['layanan_ids'])
            ->count();

        if ($servicesInPackage !== count($validated['layanan_ids'])) {
            return back()->with('status', [
                'type' => 'error',
                'action' => 'Error',
                'text' => 'All selected services must belong to the selected package!'
            ]);
        }

        // Check for duplicate service combinations
        $existingFusion = FusionService::where('paket_layanan_id', $validated['paket_layanan_id'])
            ->get()
            ->filter(function ($fusion) use ($validated) {
                $existingIds = collect($fusion->layanan_ids)->sort()->values();
                $newIds = collect($validated['layanan_ids'])->sort()->values();
                return $existingIds->equals($newIds);
            })
            ->first();

        if ($existingFusion) {
            return back()->with('status', [
                'type' => 'error',
                'action' => 'Error',
                'text' => 'A fusion service with this exact combination of services already exists!'
            ]);
        }

        FusionService::create($validated);

        return to_route('fusion-services.index')->with('status', [
            'type' => 'success',
            'action' => 'Success',
            'text' => 'Fusion service has been created successfully!'
        ]);
    }

    /**
     * Display the specified fusion service.
     */
    public function show($id)
    {
        $fusionService = FusionService::with(['paketLayanan'])->findOrFail($id);
        
        // Get service details
        $services = $fusionService->services();
        $priceBreakdown = $fusionService->getPriceBreakdown();
        $validationErrors = $fusionService->validateServices();

        return response()->json([
            'fusionService' => $fusionService,
            'services' => $services,
            'priceBreakdown' => $priceBreakdown,
            'validationErrors' => $validationErrors,
            'hasCompatibleInputs' => $fusionService->hasCompatibleInputs(),
        ]);
    }

    /**
     * Update the specified fusion service.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_fusion' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'paket_layanan_id' => 'required|exists:paket_layanans,id',
            'layanan_ids' => 'required|array|min:2|max:5',
            'layanan_ids.*' => 'exists:layanans,id',
            'custom_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'display_order' => 'integer|min:0'
        ]);

        $fusionService = FusionService::findOrFail($id);

        // Validate that all services belong to the selected package
        $servicesInPackage = Layanan::where('paket_layanan_id', $validated['paket_layanan_id'])
            ->whereIn('id', $validated['layanan_ids'])
            ->count();

        if ($servicesInPackage !== count($validated['layanan_ids'])) {
            return back()->with('status', [
                'type' => 'error',
                'action' => 'Error',
                'text' => 'All selected services must belong to the selected package!'
            ]);
        }

        // Check for duplicate service combinations (excluding current record)
        $existingFusion = FusionService::where('paket_layanan_id', $validated['paket_layanan_id'])
            ->where('id', '!=', $id)
            ->get()
            ->filter(function ($fusion) use ($validated) {
                $existingIds = collect($fusion->layanan_ids)->sort()->values();
                $newIds = collect($validated['layanan_ids'])->sort()->values();
                return $existingIds->equals($newIds);
            })
            ->first();

        if ($existingFusion) {
            return back()->with('status', [
                'type' => 'error',
                'action' => 'Error',
                'text' => 'A fusion service with this exact combination of services already exists!'
            ]);
        }

        // Check if there are active orders before allowing major changes
        $hasActiveOrders = \App\Models\Pembelian::where('fusion_id', $id)
            ->whereIn('status', ['processing', 'pending'])
            ->exists();

        if ($hasActiveOrders && (
            $fusionService->paket_layanan_id != $validated['paket_layanan_id'] ||
            collect($fusionService->layanan_ids)->sort()->values() != collect($validated['layanan_ids'])->sort()->values()
        )) {
            return back()->with('status', [
                'type' => 'error',
                'action' => 'Error',
                'text' => 'Cannot modify service combination or package while there are active orders!'
            ]);
        }

        $fusionService->update($validated);

        return to_route('fusion-services.index')->with('status', [
            'type' => 'success',
            'action' => 'Success',
            'text' => 'Fusion service has been updated successfully!'
        ]);
    }

    /**
     * Remove the specified fusion service.
     */
    public function destroy($id)
    {
        $fusionService = FusionService::findOrFail($id);

        // Check if there are any orders using this fusion service
        $hasOrders = \App\Models\Pembelian::where('fusion_id', $id)->exists();

        if ($hasOrders) {
            return back()->with('status', [
                'type' => 'error',
                'action' => 'Error',
                'text' => 'Cannot delete fusion service with existing orders. Archive it instead by setting status to inactive.'
            ]);
        }

        $fusionService->delete();

        return to_route('fusion-services.index')->with('status', [
            'type' => 'success',
            'action' => 'Success',
            'text' => 'Fusion service has been deleted successfully!'
        ]);
    }

    /**
     * Validate fusion service compatibility
     */
    public function validateFusion(Request $request)
    {
        $validated = $request->validate([
            'layanan_ids' => 'required|array|min:2|max:5',
            'layanan_ids.*' => 'exists:layanans,id'
        ]);

        $services = Layanan::whereIn('id', $validated['layanan_ids'])
            ->with(['produk.provider', 'produk.inputFields'])
            ->get();

        $compatibility = [
            'compatible' => true,
            'issues' => [],
            'providers' => [],
            'input_requirements' => [],
            'estimated_price' => 0
        ];

        // Check if all services are active
        foreach ($services as $service) {
            if ($service->status !== 'active') {
                $compatibility['compatible'] = false;
                $compatibility['issues'][] = "Service '{$service->nama_layanan}' is not active";
            }
        }

        // Group by providers
        $providerGroups = $services->groupBy(function ($service) {
            return $service->produk->provider->provider_name ?? 'unknown';
        });

        foreach ($providerGroups as $provider => $providerServices) {
            $compatibility['providers'][] = [
                'name' => $provider,
                'service_count' => $providerServices->count(),
                'services' => $providerServices->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->nama_layanan,
                        'code' => $service->kode_layanan
                    ];
                })
            ];
        }

        // Check input requirements compatibility
        if ($services->isNotEmpty()) {
            $firstService = $services->first();
            $firstInputs = $firstService->produk->inputFields->pluck('name')->sort()->values();
            
            $compatibility['input_requirements'] = $firstInputs->toArray();

            foreach ($services as $service) {
                $serviceInputs = $service->produk->inputFields->pluck('name')->sort()->values();
                if (!$firstInputs->equals($serviceInputs)) {
                    $compatibility['compatible'] = false;
                    $compatibility['issues'][] = "Service '{$service->nama_layanan}' has different input requirements";
                }
            }
        }

        // Calculate estimated price
        foreach ($services as $service) {
            $compatibility['estimated_price'] += $service->harga_beli;
        }

        return response()->json([
            'success' => true,
            'compatibility' => $compatibility
        ]);
    }

    /**
     * Calculate pricing for fusion service preview
     */
    public function calculatePricing(Request $request)
    {
        $validated = $request->validate([
            'layanan_ids' => 'required|array',
            'layanan_ids.*' => 'exists:layanans,id',
            'custom_price' => 'nullable|numeric|min:0'
        ]);

        $services = Layanan::whereIn('id', $validated['layanan_ids'])->get();
        $roles = UserRole::all();

        $pricing = [
            'base_total' => 0,
            'role_pricing' => [],
            'service_breakdown' => []
        ];

        // Calculate base total
        foreach ($services as $service) {
            $pricing['base_total'] += $service->harga_beli;
            $pricing['service_breakdown'][] = [
                'id' => $service->id,
                'name' => $service->nama_layanan,
                'base_price' => $service->harga_beli
            ];
        }

        // Calculate pricing for each role
        foreach ($roles as $role) {
            $totalForRole = 0;
            $serviceDetailsForRole = [];

            foreach ($services as $service) {
                $priceForRole = $service->getHargaLayanan($role->id);
                $totalForRole += $priceForRole;
                $serviceDetailsForRole[] = [
                    'service_name' => $service->nama_layanan,
                    'price' => $priceForRole
                ];
            }

            $pricing['role_pricing'][] = [
                'role_id' => $role->id,
                'role_name' => $role->display_name,
                'total_price' => $validated['custom_price'] ?? $totalForRole,
                'service_details' => $serviceDetailsForRole
            ];
        }

        return response()->json([
            'success' => true,
            'pricing' => $pricing
        ]);
    }

    /**
     * Bulk update status for multiple fusion services
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'fusion_ids' => 'required|array',
            'fusion_ids.*' => 'exists:fusion_services,id',
            'status' => 'required|in:active,inactive'
        ]);

        DB::beginTransaction();
        try {
            $updated = FusionService::whereIn('id', $validated['fusion_ids'])
                ->update(['status' => $validated['status']]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updated} fusion services"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update fusion services: ' . $e->getMessage()
            ], 500);
        }
    }
}