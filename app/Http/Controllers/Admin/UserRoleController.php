<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\UserRole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UserRoleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'asc');

        // Validate the sort field to prevent SQL injection
        $allowedSortFields = ['id', 'role_name', 'display_name', 'is_default'];
        if (!in_array($sort, $allowedSortFields)) {
            $sort = 'id';
        }

        // Start query
        $query = UserRole::query();

        // Apply search if provided
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('role_name', 'like', "%{$search}%")
                    ->orWhere('display_name', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $query->orderBy($sort, $direction);

        // Get paginated results
        $userRoles = $query->paginate(5)->withQueryString();

        return Inertia::render('Admin/UserRoles', [
            'userRoles' => $userRoles,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_name' => 'required|string|max:255|unique:user_roles,role_name',
            'display_name' => 'required|string|max:255',
        ]);

        // Auto-set is_default to false on creation
        $validated['is_default'] = false;

        UserRole::create($validated);

        return to_route('user-roles.index')->with('status', [
            'type' => 'success',
            'action' => 'Success',
            'text' => 'New User Role has been created!'
        ]);
    }

    public function destroy(UserRole $userRole)
    {
        // Check if role is assigned to users
        if ($userRole->users()->count() > 0) {
            return back()->with('status', [
                'type' => 'error',
                'action' => 'Error',
                'text' => 'Cannot delete role that is assigned to users!'
            ]);
        }

        // Prevent deletion of the last default role
        if ($userRole->is_default && UserRole::where('is_default', true)->count() <= 1) {
            return back()->with('status', [
                'type' => 'error',
                'action' => 'Error',
                'text' => 'Cannot delete the last default role!'
            ]);
        }

        $userRole->delete();

        return to_route('user-roles.index')->with('status', [
            'type' => 'success',
            'action' => 'Success',
            'text' => 'User Role has been deleted!'
        ]);
    }

    public function toggleDefault(Request $request, UserRole $userRole)
    {
        try {
            DB::transaction(function () use ($userRole) {
                // If setting this role as default
                if (!$userRole->is_default) {
                    // First, remove default status from current default role
                    UserRole::where('is_default', true)->update(['is_default' => false]);

                    // Set this role as default
                    $userRole->update(['is_default' => true]);
                } else {
                    // If trying to unset the only default role, prevent it
                    $defaultCount = UserRole::where('is_default', true)->count();
                    if ($defaultCount <= 1) {
                        throw new \Exception('At least one default role must exist!');
                    }

                    $userRole->update(['is_default' => false]);
                }
            });

            return back()->with('status', [
                'type' => 'success',
                'action' => 'Success',
                'text' => 'Default role status updated successfully!'
            ]);
        } catch (\Exception $e) {
            return back()->with('status', [
                'type' => 'error',
                'action' => 'Error',
                'text' => $e->getMessage()
            ]);
        }
    }
}