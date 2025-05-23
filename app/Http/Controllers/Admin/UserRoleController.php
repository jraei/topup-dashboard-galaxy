
<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class UserRoleController extends Controller
{
    /**
     * Display a listing of user roles.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'sort', 'direction']);

        $query = UserRole::query();

        // Apply search filter if provided
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('role_name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%");
            });
        }

        // Apply sort if provided
        if ($request->filled('sort')) {
            $sortField = $request->input('sort');
            $sortDirection = $request->input('direction', 'asc');
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('id', 'asc');
        }

        $userRoles = $query->paginate(10)
                          ->withQueryString();

        return Inertia::render('Admin/UserRoles', [
            'userRoles' => $userRoles,
            'filters' => $filters,
        ]);
    }

    /**
     * Store a newly created user role.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|unique:user_roles,role_name|max:50',
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'is_default' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        try {
            DB::transaction(function () use ($request) {
                $isDefault = $request->has('is_default') && $request->boolean('is_default');
                
                // If setting as default, update other default roles
                if ($isDefault) {
                    UserRole::where('is_default', true)->update(['is_default' => false]);
                }
                
                UserRole::create([
                    'role_name' => $request->role_name,
                    'display_name' => $request->display_name,
                    'description' => $request->description,
                    'is_default' => $isDefault,
                    'permissions' => [],
                ]);
            });
            
            return redirect()->back()->with('success', 'User role created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to create user role: ' . $e->getMessage()]);
        }
    }

    /**
     * Update the specified user role's default status.
     */
    public function toggleDefault(UserRole $userRole)
    {
        try {
            DB::transaction(function () use ($userRole) {
                // First, unset any existing default roles
                UserRole::where('is_default', true)->update(['is_default' => false]);
                
                // Set new default role
                $userRole->is_default = true;
                $userRole->save();
            });
            
            return redirect()->back()->with('success', 'Default role updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to update default role: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified user role.
     */
    public function destroy(UserRole $userRole)
    {
        // Check if role is assigned to any users
        $userCount = User::where('user_role_id', $userRole->id)->count();
        if ($userCount > 0) {
            return redirect()->back()->withErrors(['error' => 'Cannot delete role assigned to ' . $userCount . ' user(s).']);
        }
        
        // Check if it's a default role
        if ($userRole->is_default) {
            return redirect()->back()->withErrors(['error' => 'Cannot delete the default role. Please set another role as default first.']);
        }

        try {
            $userRole->delete();
            return redirect()->back()->with('success', 'User role deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to delete user role: ' . $e->getMessage()]);
        }
    }
}
