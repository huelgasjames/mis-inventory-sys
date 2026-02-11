<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Computer;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
        $departments = Department::withCount(['assets', 'computers'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $departments
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
        $users = \App\Models\User::select('id', 'name', 'email', 'role', 'user_id')->get();
        
        return response()->json([
            'success' => true,
            'data' => $users
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
}
