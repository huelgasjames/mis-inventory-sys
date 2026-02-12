<?php

namespace App\Http\Controllers;

use App\Models\Laboratory;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LaboratoryController extends Controller
{
    /**
     * Get all laboratories
     */
    public function index(): JsonResponse
    {
        try {
            $laboratories = Laboratory::with(['department', 'inChargeUser'])->get();
            
            return response()->json([
                'success' => true,
                'data' => $laboratories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching laboratories: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create laboratory
     */
    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'department_id' => 'nullable|exists:departments,id',
            'location' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:Active,Maintenance,Closed',
            'in_charge_user_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ]);

        $laboratory = Laboratory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Laboratory created successfully',
            'data' => $laboratory->load(['department', 'inChargeUser'])
        ]);
    }

    /**
     * Update laboratory
     */
    public function update(Request $request, $id): JsonResponse
    {
        $laboratory = Laboratory::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string',
            'department_id' => 'nullable|exists:departments,id',
            'location' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:Active,Maintenance,Closed',
            'in_charge_user_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ]);

        $laboratory->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Laboratory updated successfully',
            'data' => $laboratory->load(['department', 'inChargeUser'])
        ]);
    }

    /**
     * Delete laboratory
     */
    public function delete($id): JsonResponse
    {
        try {
            $laboratory = Laboratory::findOrFail($id);
            $laboratory->delete();

            return response()->json([
                'success' => true,
                'message' => 'Laboratory deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting laboratory: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get laboratory statistics
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_laboratories' => Laboratory::count(),
                'active' => Laboratory::where('status', 'Active')->count(),
                'maintenance' => Laboratory::where('status', 'Maintenance')->count(),
                'closed' => Laboratory::where('status', 'Closed')->count(),
                'by_department' => $this->getLaboratoriesByDepartment(),
                'capacity_distribution' => $this->getCapacityDistribution(),
            ]
        ]);
    }

    private function getLaboratoriesByDepartment(): array
    {
        return Laboratory::join('departments', 'laboratories.department_id', '=', 'departments.id')
            ->selectRaw('departments.name, COUNT(*) as count')
            ->groupBy('departments.name')
            ->pluck('count', 'departments.name')
            ->toArray();
    }

    private function getCapacityDistribution(): array
    {
        $distribution = [
            'small' => Laboratory::where('capacity', '<=', 20)->count(),
            'medium' => Laboratory::whereBetween('capacity', [21, 40])->count(),
            'large' => Laboratory::whereBetween('capacity', [41, 60])->count(),
            'extra_large' => Laboratory::where('capacity', '>', 60)->count(),
        ];

        return $distribution;
    }
}
