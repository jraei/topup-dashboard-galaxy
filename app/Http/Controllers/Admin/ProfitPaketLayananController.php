<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Layanan;
use App\Models\UserRole;
use App\Models\PaketLayanan;
use Illuminate\Http\Request;
use App\Models\ProfitPaketLayanan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ProfitPaketLayananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'asc');
        $packageFilter = $request->input('package');
        $roleFilter = $request->input('role');
        $typeFilter = $request->input('type');

        // Validate the sort field to prevent SQL injection
        $allowedSortFields = ['id', 'type', 'value', 'created_at'];
        if (!in_array($sort, $allowedSortFields)) {
            $sort = 'id';
        }

        // Start query
        $query = ProfitPaketLayanan::with(['paketLayanan', 'user_role']);

        // Apply filters
        if ($search) {
            $query->whereHas('paketLayanan', function ($q) use ($search) {
                $q->where('judul_paket', 'like', "%{$search}%");
            })->orWhereHas('user_role', function ($q) use ($search) {
                $q->where('display_name', 'like', "%{$search}%");
            });
        }

        if ($packageFilter) {
            $query->where('paket_layanan_id', $packageFilter);
        }

        if ($roleFilter) {
            $query->where('user_roles_id', $roleFilter);
        }

        if ($typeFilter) {
            $query->where('type', $typeFilter);
        }

        // Apply sorting
        $query->orderBy($sort, $direction);

        // Get paginated results
        $profitPaketLayanans = $query->paginate(10)->withQueryString();

        // Get active packages and user roles for form
        $packages = PaketLayanan::with('produk')->get();
        $roles = UserRole::all();

        return Inertia::render('Admin/ProfitPaketLayanan', [
            'profitPaketLayanans' => $profitPaketLayanans,
            'packages' => $packages,
            'roles' => $roles,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction,
                'package' => $packageFilter,
                'role' => $roleFilter,
                'type' => $typeFilter
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'paket_layanan_id' => 'required|exists:paket_layanans,id',
            'user_roles_id' => 'required|exists:user_roles,id',
            'type' => 'required|in:percent,multiplier',
            'value' => 'required|numeric|min:0.01'
        ]);

        // Check if this package+role combination already exists
        $exists = ProfitPaketLayanan::where('paket_layanan_id', $validated['paket_layanan_id'])
            ->where('user_roles_id', $validated['user_roles_id'])
            ->exists();

        if ($exists) {
            return back()->with('status', [
                'type' => 'error',
                'action' => 'Error',
                'text' => 'Profit setting for this package and role already exists!'
            ]);
        }

        ProfitPaketLayanan::create($validated);

        return to_route('profit-paket-layanans.index')->with('status', [
            'type' => 'success',
            'action' => 'Success',
            'text' => 'New Profit Setting has been added!'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $profitPaketLayanan = ProfitPaketLayanan::with(['paketLayanan', 'user_role'])->find($id);

        return response()->json([
            'profitPaketLayanan' => $profitPaketLayanan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'paket_layanan_id' => 'required|exists:paket_layanans,id',
            'user_roles_id' => 'required|exists:user_roles,id',
            'type' => 'required|in:percent,multiplier',
            'value' => 'required|numeric|min:0.01'
        ]);

        // Check if this package+role combination already exists for other records
        $exists = ProfitPaketLayanan::where('paket_layanan_id', $validated['paket_layanan_id'])
            ->where('user_roles_id', $validated['user_roles_id'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->with('status', [
                'type' => 'error',
                'action' => 'Error',
                'text' => 'Profit setting for this package and role already exists!'
            ]);
        }

        $profitPaketLayanan = ProfitPaketLayanan::findOrFail($id);
        $profitPaketLayanan->update($validated);

        return to_route('profit-paket-layanans.index')->with('status', [
            'type' => 'success',
            'action' => 'Success',
            'text' => 'Profit Setting has been updated!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $profitPaketLayanan = ProfitPaketLayanan::findOrFail($id);
        $profitPaketLayanan->delete();

        return to_route('profit-paket-layanans.index')->with('status', [
            'type' => 'success',
            'action' => 'Success',
            'text' => 'Profit Setting has been deleted!'
        ]);
    }

    /**
     * Bulk create or update profit settings for multiple packages
     */
    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'package_ids' => 'required|array|min:1',
            'package_ids.*' => 'exists:paket_layanans,id',
            'user_roles_id' => 'required|exists:user_roles,id',
            'type' => 'required|in:percent,multiplier',
            'value' => 'required|numeric|min:0.01'
        ]);

        DB::beginTransaction();
        try {
            $created = 0;
            $skipped = 0;
            $errors = [];

            foreach ($validated['package_ids'] as $packageId) {
                // Check if setting already exists
                $existingProfit = ProfitPaketLayanan::where('paket_layanan_id', $packageId)
                    ->where('user_roles_id', $validated['user_roles_id'])
                    ->first();

                if ($existingProfit) {
                    $skipped++;
                    continue;
                }

                try {
                    ProfitPaketLayanan::create([
                        'paket_layanan_id' => $packageId,
                        'user_roles_id' => $validated['user_roles_id'],
                        'type' => $validated['type'],
                        'value' => $validated['value']
                    ]);
                    $created++;
                } catch (\Exception $e) {
                    $package = PaketLayanan::find($packageId);
                    $errors[] = "Failed to create profit setting for package: " . ($package->judul_paket ?? "ID {$packageId}");
                }
            }

            if (!empty($errors)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Bulk operation failed',
                    'errors' => $errors
                ], 422);
            }

            DB::commit();

            $message = "Bulk operation completed: {$created} created";
            if ($skipped > 0) {
                $message .= ", {$skipped} skipped (already exists)";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'stats' => [
                    'created' => $created,
                    'skipped' => $skipped
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create bulk profit settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk create or update profit settings for multiple packages
     */
    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'package_ids' => 'required|array',
            'package_ids.*' => 'exists:paket_layanans,id',
            'user_roles_id' => 'required|exists:user_roles,id',
            'type' => 'required|in:percent,multiplier',
            'value' => 'required|numeric|min:0.01'
        ]);

        DB::beginTransaction();
        try {
            $created = 0;
            $updated = 0;

            foreach ($validated['package_ids'] as $packageId) {
                // Check if setting exists
                $existingProfit = ProfitPaketLayanan::where('paket_layanan_id', $packageId)
                    ->where('user_roles_id', $validated['user_roles_id'])
                    ->first();

                if ($existingProfit) {
                    // Update existing
                    $existingProfit->update([
                        'type' => $validated['type'],
                        'value' => $validated['value']
                    ]);
                    $updated++;
                } else {
                    // Create new
                    ProfitPaketLayanan::create([
                        'paket_layanan_id' => $packageId,
                        'user_roles_id' => $validated['user_roles_id'],
                        'type' => $validated['type'],
                        'value' => $validated['value']
                    ]);
                    $created++;
                }
            }

            DB::commit();

            $message = "Successfully processed profit settings: {$created} created, {$updated} updated";
            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update profit settings: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Preview prices for a specific package across all roles.
     */
    public function preview(Request $request)
    {
        $validated = $request->validate([
            'paket_layanan_id' => 'required|exists:paket_layanans,id'
        ]);

        // Get the package
        $package = PaketLayanan::with('produk')
            ->where('id', $validated['paket_layanan_id'])
            ->first();

        if (!$package) {
            return response()->json([
                'success' => false,
                'message' => 'Package not found'
            ], 404);
        }

        // Get all roles
        $roles = UserRole::all();

        // Get all profit settings for this package
        $profitSettings = ProfitPaketLayanan::where('paket_layanan_id', $package->id)
            ->with('user_role')
            ->get()
            ->keyBy('user_roles_id');

        // Get services in this package
        $services = Layanan::where('paket_layanan_id', $package->id)
            ->where('status', 'active')
            ->get();

        // Calculate prices for each service and role
        $pricing = [];

        foreach ($services as $service) {
            $priceInfo = [
                'id' => $service['id'],
                'name' => $service['nama_layanan'],
                'code' => $service['kode_layanan'],
                'base_price' => $service['harga_beli'],
                'role_prices' => []
            ];

            foreach ($roles as $role) {
                $finalPrice = $service['harga_beli'];
                $profitType = null;
                $profitValue = null;

                // Check if there's a profit rule for this role and package
                if (isset($profitSettings[$role->id])) {
                    $setting = $profitSettings[$role->id];
                    $profitType = $setting->type;
                    $profitValue = $setting->value;

                    // Calculate price using the profit rule
                    $finalPrice = $setting->calculatePrice($service['harga_beli']);
                }

                $priceInfo['role_prices'][] = [
                    'role_id' => $role->id,
                    'role_name' => $role->display_name,
                    'final_price' => $finalPrice,
                    'profit_type' => $profitType,
                    'profit_value' => $profitValue
                ];
            }

            $pricing[] = $priceInfo;
        }

        return response()->json([
            'success' => true,
            'package' => [
                'id' => $package->id,
                'name' => $package->judul_paket,
                'description' => $package->informasi,
                'product' => $package->produk ? [
                    'id' => $package->produk->id,
                    'name' => $package->produk->nama,
                    'thumbnail' => $package->produk->thumbnail,
                ] : null,
            ],
            'pricing' => $pricing,
        ]);
    }
}
