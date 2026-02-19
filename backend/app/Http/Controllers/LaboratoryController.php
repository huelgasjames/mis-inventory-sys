<?php

namespace App\Http\Controllers;

use App\Models\Laboratory;
use App\Models\User;
use App\Models\Department;
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
            $laboratories = Laboratory::with(['headOfLab', 'department', 'computers'])
                ->get()
                ->map(function ($lab) {
                    $lab->current_computer_count = $lab->computers->count();
                    $lab->available_capacity = $lab->capacity - $lab->current_computer_count;
                    return $lab;
                });
            
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
            'department_id' => 'nullable|integer|min:1|exists:departments,id',
            'lab_code' => 'required|string|unique:laboratories,lab_code',
            'lab_name' => 'required|string',
            'location' => 'required|string',
            'building' => 'required|string',
            'floor' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'head_of_lab' => 'nullable|integer|min:1|exists:users,id',
            'contact_number' => 'required|string',
            'email' => 'required|email',
            'is_active' => 'boolean',
        ]);

        // Auto-populate contact info from department if not provided
        if (empty($validated['contact_number']) || empty($validated['email'])) {
            if (!empty($validated['department_id'])) {
                $department = Department::find($validated['department_id']);
                if ($department) {
                    // Get department head (assuming there's a head_of_department field or similar)
                    $deptHead = User::where('department_id', $department->id)
                        ->where('role', 'department_head')
                        ->first();
                    
                    if ($deptHead) {
                        $validated['contact_number'] = $validated['contact_number'] ?? $deptHead->phone_number ?? null;
                        $validated['email'] = $validated['email'] ?? $deptHead->email ?? null;
                    }
                }
            }
        }

        $laboratory = Laboratory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Laboratory created successfully',
            'data' => $laboratory->load(['headOfLab', 'department'])
        ]);
    }

    /**
     * Update laboratory
     */
    public function update(Request $request, $id): JsonResponse
    {
        $laboratory = Laboratory::findOrFail($id);
        
        $validated = $request->validate([
            'department_id' => 'nullable|integer|min:1|exists:departments,id',
            'lab_code' => 'required|string|unique:laboratories,lab_code,'.$id,
            'lab_name' => 'required|string',
            'location' => 'required|string',
            'building' => 'required|string',
            'floor' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'head_of_lab' => 'nullable|integer|min:1|exists:users,id',
            'contact_number' => 'required|string',
            'email' => 'required|email',
            'is_active' => 'boolean',
        ]);

        $laboratory->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Laboratory updated successfully',
            'data' => $laboratory->load(['headOfLab', 'department'])
        ]);
    }

    /**
     * Delete laboratory
     */
    public function delete($id): JsonResponse
    {
        try {
            $Laboratory = Laboratory::findOrFail($id);
            $Laboratory->delete();

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
                'active' => Laboratory::where('is_active', true)->count(),
                'inactive' => Laboratory::where('is_active', false)->count(),
                'capacity_distribution' => $this->getCapacityDistribution(),
            ]
        ]);
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
