<?php

namespace App\Http\Controllers;

use App\Models\Deployment;
use App\Models\Computer;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DeploymentController extends Controller
{
    /**
     * Get all deployments
     */
    public function index(): JsonResponse
    {
        try {
            $deployments = Deployment::with(['computer', 'department', 'user'])
                ->orderBy('deployment_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $deployments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching deployments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new deployment
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'computer_id' => 'required|exists:computers,id',
                'department_id' => 'required|exists:departments,id',
                'user_id' => 'nullable|exists:users,id',
                'location' => 'nullable|string|max:255',
                'status' => 'required|in:deployed,returned,maintenance,retired',
                'deployment_date' => 'required|date',
                'return_date' => 'nullable|date|after_or_equal:deployment_date',
                'notes' => 'nullable|string',
                'deployed_by' => 'required|string|max:255',
            ]);

            // Check if computer is already deployed
            $existingDeployment = Deployment::where('computer_id', $validated['computer_id'])
                ->where('status', 'deployed')
                ->first();

            if ($existingDeployment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Computer is already deployed'
                ], 422);
            }

            $deployment = Deployment::create($validated);
            $deployment->load(['computer', 'department', 'user']);

            return response()->json([
                'success' => true,
                'data' => $deployment,
                'message' => 'Deployment created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating deployment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific deployment
     */
    public function show(string $id): JsonResponse
    {
        try {
            $deployment = Deployment::with(['computer', 'department', 'user'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $deployment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Deployment not found'
            ], 404);
        }
    }

    /**
     * Update a deployment
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $deployment = Deployment::findOrFail($id);

            $validated = $request->validate([
                'computer_id' => 'sometimes|exists:computers,id',
                'department_id' => 'sometimes|exists:departments,id',
                'user_id' => 'nullable|sometimes|exists:users,id',
                'location' => 'nullable|string|max:255',
                'status' => 'sometimes|in:deployed,returned,maintenance,retired',
                'deployment_date' => 'sometimes|date',
                'return_date' => 'nullable|date|after_or_equal:deployment_date',
                'notes' => 'nullable|string',
                'deployed_by' => 'sometimes|string|max:255',
            ]);

            $deployment->update($validated);
            $deployment->load(['computer', 'department', 'user']);

            return response()->json([
                'success' => true,
                'data' => $deployment,
                'message' => 'Deployment updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating deployment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a deployment
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $deployment = Deployment::findOrFail($id);
            $deployment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Deployment deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting deployment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get deployment statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'total_deployments' => Deployment::count(),
                'deployed' => Deployment::deployed()->count(),
                'returned' => Deployment::returned()->count(),
                'maintenance' => Deployment::maintenance()->count(),
                'retired' => Deployment::retired()->count(),
                'by_department' => Deployment::with('department')
                    ->selectRaw('department_id, COUNT(*) as count')
                    ->groupBy('department_id')
                    ->get()
                    ->mapWithKeys(function ($item) {
                        return [$item->department->name => $item->count];
                    }),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching deployment stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Return a deployed computer
     */
    public function returnComputer(Request $request, string $id): JsonResponse
    {
        try {
            $deployment = Deployment::findOrFail($id);

            if ($deployment->status !== 'deployed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Computer is not currently deployed'
                ], 422);
            }

            $validated = $request->validate([
                'return_date' => 'required|date',
                'notes' => 'nullable|string',
            ]);

            $deployment->update([
                'status' => 'returned',
                'return_date' => $validated['return_date'],
                'notes' => $validated['notes'] ?? $deployment->notes,
            ]);

            // Update computer status to available
            $deployment->computer->update(['status' => 'Working']);

            return response()->json([
                'success' => true,
                'data' => $deployment->load(['computer', 'department', 'user']),
                'message' => 'Computer returned successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error returning computer: ' . $e->getMessage()
            ], 500);
        }
    }
}
