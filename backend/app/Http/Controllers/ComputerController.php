<?php

namespace App\Http\Controllers;

use App\Models\Computer;
use App\Models\Asset;
use App\Models\Department;
use App\Models\Laboratory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ComputerController extends Controller
{
    /**
     * Get all computers
     */
    public function index(): JsonResponse
    {
        try {
            $computers = Computer::with(['department', 'processor', 'motherboard', 'ram', 'storage', 'videoCard', 'dvdRom', 'psu', 'assignedUser'])->get();
            
            return response()->json([
                'success' => true,
                'data' => $computers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching computers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create computer asset
     */
    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'computer_name' => 'required|string',
            'asset_tag' => 'required|string',
            'pc_number' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'processor_id' => 'nullable|exists:processors,id',
            'motherboard_id' => 'nullable|exists:motherboards,id',
            'ram_id' => 'nullable|exists:rams,id',
            'storage_id' => 'nullable|exists:storages,id',
            'video_card_id' => 'nullable|exists:video_cards,id',
            'psu_id' => 'nullable|exists:psus,id',
            'dvd_rom_id' => 'nullable|exists:dvd_roms,id',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:Working,Defective,For Disposal',
            'location' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $computer = Computer::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Computer created successfully',
            'data' => $computer
        ]);
    }

    /**
     * Update computer
     */
    public function update(Request $request, $id): JsonResponse
    {
        $computer = Computer::findOrFail($id);
        
        $validated = $request->validate([
            'computer_name' => 'required|string',
            'asset_tag' => 'required|string',
            'pc_number' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'processor_id' => 'nullable|exists:processors,id',
            'motherboard_id' => 'nullable|exists:motherboards,id',
            'ram_id' => 'nullable|exists:rams,id',
            'storage_id' => 'nullable|exists:storages,id',
            'video_card_id' => 'nullable|exists:video_cards,id',
            'psu_id' => 'nullable|exists:psus,id',
            'dvd_rom_id' => 'nullable|exists:dvd_roms,id',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:Working,Defective,For Disposal',
            'location' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $computer->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Computer updated successfully',
            'data' => $computer->load(['department', 'assignedUser'])
        ]);
    }

    /**
     * Delete computer
     */
    public function delete($id): JsonResponse
    {
        try {
            $computer = Computer::findOrFail($id);
            $computer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Computer deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting computer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deploy computer
     */
    public function deploy(Request $request, $id): JsonResponse
    {
        $computer = Computer::findOrFail($id);
        $asset = Asset::where('asset_tag', $computer->asset_tag)->first();

        $validated = $request->validate([
            'deploy_to' => 'required|in:department,laboratory',
            'department_id' => 'required_if:deploy_to,department|exists:departments,id',
            'laboratory_id' => 'required_if:deploy_to,laboratory|exists:laboratories,id',
        ]);

        if ($validated['deploy_to'] === 'department') {
            $computer->department_id = $validated['department_id'];
            $computer->laboratory_id = null;
            $asset->department_id = $validated['department_id'];
        } else {
            $computer->laboratory_id = $validated['laboratory_id'];
            $computer->department_id = null;
            $asset->department_id = null;
        }

        $computer->status = 'Deployed';
        $asset->status = 'Deployed';

        $computer->save();
        $asset->save();

        return response()->json([
            'success' => true,
            'message' => 'Computer deployed successfully',
            'data' => $computer->load(['department', 'laboratory'])
        ]);
    }

    /**
     * Transfer computer
     */
    public function transfer(Request $request, $id): JsonResponse
    {
        $computer = Computer::findOrFail($id);
        $asset = Asset::where('asset_tag', $computer->asset_tag)->first();

        $validated = $request->validate([
            'transfer_to' => 'required|in:department,laboratory',
            'department_id' => 'required_if:transfer_to,department|exists:departments,id',
            'laboratory_id' => 'required_if:transfer_to,laboratory|exists:laboratories,id',
            'notes' => 'nullable|string',
        ]);

        // Log transfer
        $this->logTransfer($computer, $validated);

        // Update location
        if ($validated['transfer_to'] === 'department') {
            $computer->department_id = $validated['department_id'];
            $computer->laboratory_id = null;
            $asset->department_id = $validated['department_id'];
        } else {
            $computer->laboratory_id = $validated['laboratory_id'];
            $computer->department_id = null;
            $asset->department_id = null;
        }

        $computer->save();
        $asset->save();

        return response()->json([
            'success' => true,
            'message' => 'Computer transferred successfully',
            'data' => $computer->load(['department', 'laboratory'])
        ]);
    }

    /**
     * Log repair record
     */
    public function logRepair(Request $request, $id): JsonResponse
    {
        $computer = Computer::findOrFail($id);
        $asset = Asset::where('asset_tag', $computer->asset_tag)->first();

        $validated = $request->validate([
            'issue_description' => 'required|string',
            'repair_action' => 'required|string',
            'cost' => 'nullable|numeric',
            'technician' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // Create maintenance record
        \App\Models\MaintenanceRecord::create([
            'computer_id' => $computer->id,
            'asset_tag' => $computer->asset_tag,
            'issue_description' => $validated['issue_description'],
            'repair_action' => $validated['repair_action'],
            'cost' => $validated['cost'] ?? 0,
            'technician' => $validated['technician'],
            'notes' => $validated['notes'] ?? '',
            'status' => 'In Progress',
            'created_by' => auth()->id() ?? 1,
        ]);

        // Update computer status
        $computer->status = 'Under Repair';
        $asset->status = 'Under Repair';

        $computer->save();
        $asset->save();

        return response()->json([
            'success' => true,
            'message' => 'Repair logged successfully',
            'data' => $computer
        ]);
    }

    /**
     * Complete repair
     */
    public function completeRepair($id): JsonResponse
    {
        $computer = Computer::findOrFail($id);
        $asset = Asset::where('asset_tag', $computer->asset_tag)->first();

        // Update maintenance record
        $maintenance = \App\Models\MaintenanceRecord::where('computer_id', $id)
            ->where('status', 'In Progress')
            ->first();

        if ($maintenance) {
            $maintenance->status = 'Completed';
            $maintenance->completed_at = now();
            $maintenance->save();
        }

        // Update computer status
        $computer->status = 'Available';
        $asset->status = 'Available';

        $computer->save();
        $asset->save();

        return response()->json([
            'success' => true,
            'message' => 'Repair completed successfully',
            'data' => $computer
        ]);
    }

    /**
     * Get computer statistics
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_computers' => Computer::count(),
                'available' => Computer::where('status', 'Available')->count(),
                'deployed' => Computer::where('status', 'Deployed')->count(),
                'under_repair' => Computer::where('status', 'Under Repair')->count(),
                'by_department' => $this->getComputersByDepartment(),
                'by_laboratory' => $this->getComputersByLaboratory(),
            ]
        ]);
    }

    private function generateAssetTag(): string
    {
        do {
            $tag = 'PC' . str_pad(Computer::count() + 1, 4, '0', STR_PAD_LEFT);
        } while (Computer::where('asset_tag', $tag)->exists());

        return $tag;
    }

    private function getComponentName($table, $id): ?string
    {
        if (!$id) return null;
        
        $component = \DB::table($table)->find($id);
        return $component ? $component->model ?? $component->type ?? null : null;
    }

    private function updateComponentStatuses($validated): void
    {
        $components = [
            'processors' => $validated['processor_id'] ?? null,
            'motherboards' => $validated['motherboard_id'] ?? null,
            'rams' => $validated['ram_id'] ?? null,
            'storages' => $validated['storage_id'] ?? null,
            'video_cards' => $validated['video_card_id'] ?? null,
            'psus' => $validated['psu_id'] ?? null,
            'dvd_roms' => $validated['dvd_rom_id'] ?? null,
        ];

        foreach ($components as $table => $id) {
            if ($id) {
                \DB::table($table)->where('id', $id)->update(['status' => 'In Use']);
            }
        }
    }

    private function logTransfer($computer, $validated): void
    {
        \App\Models\Deployment::create([
            'computer_id' => $computer->id,
            'asset_tag' => $computer->asset_tag,
            'from_department_id' => $computer->department_id,
            'from_laboratory_id' => $computer->laboratory_id,
            'to_department_id' => $validated['department_id'] ?? null,
            'to_laboratory_id' => $validated['laboratory_id'] ?? null,
            'transfer_type' => 'transfer',
            'notes' => $validated['notes'] ?? '',
            'created_by' => auth()->id() ?? 1,
        ]);
    }

    private function getComputersByDepartment(): array
    {
        return Computer::join('departments', 'computers.department_id', '=', 'departments.id')
            ->selectRaw('departments.name, COUNT(*) as count')
            ->groupBy('departments.name')
            ->pluck('count', 'departments.name')
            ->toArray();
    }

    private function getComputersByLaboratory(): array
    {
        return Computer::join('laboratories', 'computers.laboratory_id', '=', 'laboratories.id')
            ->selectRaw('laboratories.name, COUNT(*) as count')
            ->groupBy('laboratories.name')
            ->pluck('count', 'laboratories.name')
            ->toArray();
    }
}
