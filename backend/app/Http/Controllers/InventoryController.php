<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Computer;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
    /**
     * Display the inventory page
     */
    public function index()
    {
        return view('inventory.index');
    }

    /**
     * Get all assets for API
     */
    public function getAssets(): JsonResponse
    {
        $assets = Asset::with(['department', 'assignedUser'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $assets
        ]);
    }

    /**
     * Get all computers for API
     */
    public function getComputers(): JsonResponse
    {
        $computers = Computer::with(['department', 'assignedUser'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $computers
        ]);
    }

    /**
     * Get all departments for API
     */
    public function getDepartments(): JsonResponse
    {
        $departments = Department::with(['category'])
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $departments
        ]);
    }

    /**
     * Get all department categories for API
     */
    public function getDepartmentCategories(): JsonResponse
    {
        $categories = \App\Models\DepartmentCategory::all();
        
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get inventory statistics
     */
    public function getStats(): JsonResponse
    {
        $totalAssets = Asset::count();
        $totalComputers = Computer::count();
        $totalDepartments = Department::count();
        $workingAssets = Asset::where('status', 'Working')->count();
        $defectiveAssets = Asset::where('status', 'Defective')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'totalAssets' => $totalAssets,
                'totalComputers' => $totalComputers,
                'totalDepartments' => $totalDepartments,
                'workingAssets' => $workingAssets,
                'defectiveAssets' => $defectiveAssets,
            ]
        ]);
    }

    /**
     * Get all users for API
     */
    public function getUsers(): JsonResponse
    {
        $users = User::with(['department', 'assets'])->get();
        
        // Add assigned assets count to each user
        $users->each(function ($user) {
            $user->assigned_assets_count = $user->assets->count();
        });
        
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Create a new user
     */
    public function createUser(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,faculty,staff,student',
            'address' => 'required|string',
            'birthdate' => 'nullable|date|before:today',
            'contact_person' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'department_id' => 'nullable|exists:departments,id',
            'is_active' => 'boolean',
            'assigned_assets_count' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $validated['password'] = bcrypt($validated['password']);
        $validated['user_id'] = 'USER' . str_pad((User::max('id') + 1), 6, '0', STR_PAD_LEFT);
        
        // Set default value for is_active if not provided
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        // Set default value for assigned_assets_count if not provided
        if (!isset($validated['assigned_assets_count'])) {
            $validated['assigned_assets_count'] = 0;
        }

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    /**
     * Update a user
     */
    public function updateUser(Request $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,faculty,staff,student',
            'address' => 'required|string',
            'birthdate' => 'nullable|date|before:today',
            'contact_person' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'department_id' => 'nullable|exists:departments,id',
            'is_active' => 'boolean',
            'assigned_assets_count' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Only hash password if it's provided
        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    /**
     * Delete a user
     */
    public function deleteUser($id): JsonResponse
    {
        $user = User::findOrFail($id);
        
        // Check if user has assigned assets
        if ($user->assets()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete user with assigned assets. Please reassign assets first.'
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Store a new asset
     */
    public function storeAsset(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'asset_tag' => 'required|string|unique:assets',
            'computer_name' => 'required|string',
            'status' => 'required|in:Working,Defective,For Disposal',
            'department_id' => 'nullable|exists:departments,id',
            'assigned_to' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ]);

        $asset = Asset::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Asset created successfully',
            'data' => $asset
        ]);
    }

    /**
     * Update an asset
     */
    public function updateAsset(Request $request, $id): JsonResponse
    {
        $asset = Asset::findOrFail($id);

        $validated = $request->validate([
            'asset_tag' => 'required|string|unique:assets,asset_tag,' . $id,
            'computer_name' => 'required|string',
            'status' => 'required|in:Working,Defective,For Disposal',
            'department_id' => 'nullable|exists:departments,id',
            'assigned_to' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ]);

        $asset->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Asset updated successfully',
            'data' => $asset
        ]);
    }

    /**
     * Delete an asset
     */
    public function deleteAsset($id): JsonResponse
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();

        return response()->json([
            'success' => true,
            'message' => 'Asset deleted successfully'
        ]);
    }

    /**
     * Create a new department
     */
    public function createDepartment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:department_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        
        $department = Department::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Department created successfully',
            'data' => $department
        ], 201);
    }

    /**
     * Update a department
     */
    public function updateDepartment(Request $request, $id): JsonResponse
    {
        $department = Department::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:departments,name,' . $id,
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:department_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $department->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Department updated successfully',
            'data' => $department
        ]);
    }

    /**
     * Delete a department
     */
    public function deleteDepartment($id): JsonResponse
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return response()->json([
            'success' => true,
            'message' => 'Department deleted successfully'
        ]);
    }
}
