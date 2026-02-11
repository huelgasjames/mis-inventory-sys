<?php

namespace App\Http\Controllers;

use App\Models\Computer;
use App\Models\Asset;
use App\Models\Department;
use App\Models\Laboratory;
use App\Models\MaintenanceRecord;
use App\Models\Deployment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    /**
     * Get PCs per department report
     */
    public function pcsPerDepartment(): JsonResponse
    {
        $departments = Department::withCount(['computers', 'assets'])->get();
        
        $data = $departments->map(function ($dept) {
            return [
                'department' => $dept->name,
                'total_computers' => $dept->computers_count,
                'total_assets' => $dept->assets_count,
                'available' => $dept->computers()->where('status', 'Available')->count(),
                'deployed' => $dept->computers()->where('status', 'Deployed')->count(),
                'under_repair' => $dept->computers()->where('status', 'Under Repair')->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get PCs per laboratory report
     */
    public function pcsPerLaboratory(): JsonResponse
    {
        $laboratories = Laboratory::withCount('computers')->get();
        
        $data = $laboratories->map(function ($lab) {
            return [
                'laboratory' => $lab->name,
                'total_computers' => $lab->computers_count,
                'available' => $lab->computers()->where('status', 'Available')->count(),
                'deployed' => $lab->computers()->where('status', 'Deployed')->count(),
                'under_repair' => $lab->computers()->where('status', 'Under Repair')->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get component usage report
     */
    public function componentUsage(): JsonResponse
    {
        $data = [
            'processors' => [
                'total' => \App\Models\Processor::count(),
                'available' => \App\Models\Processor::where('status', 'Available')->count(),
                'in_use' => \App\Models\Processor::where('status', 'In Use')->count(),
                'defective' => \App\Models\Processor::where('status', 'Defective')->count(),
            ],
            'motherboards' => [
                'total' => \App\Models\Motherboard::count(),
                'available' => \App\Models\Motherboard::where('status', 'Available')->count(),
                'in_use' => \App\Models\Motherboard::where('status', 'In Use')->count(),
                'defective' => \App\Models\Motherboard::where('status', 'Defective')->count(),
            ],
            'rams' => [
                'total' => \App\Models\Ram::count(),
                'available' => \App\Models\Ram::where('status', 'Available')->count(),
                'in_use' => \App\Models\Ram::where('status', 'In Use')->count(),
                'defective' => \App\Models\Ram::where('status', 'Defective')->count(),
            ],
            'storages' => [
                'total' => \App\Models\Storage::count(),
                'available' => \App\Models\Storage::where('status', 'Available')->count(),
                'in_use' => \App\Models\Storage::where('status', 'In Use')->count(),
                'defective' => \App\Models\Storage::where('status', 'Defective')->count(),
            ],
            'video_cards' => [
                'total' => \App\Models\VideoCard::count(),
                'available' => \App\Models\VideoCard::where('status', 'Available')->count(),
                'in_use' => \App\Models\VideoCard::where('status', 'In Use')->count(),
                'defective' => \App\Models\VideoCard::where('status', 'Defective')->count(),
            ],
            'psus' => [
                'total' => \App\Models\Psu::count(),
                'available' => \App\Models\Psu::where('status', 'Available')->count(),
                'in_use' => \App\Models\Psu::where('status', 'In Use')->count(),
                'defective' => \App\Models\Psu::where('status', 'Defective')->count(),
            ],
            'dvd_roms' => [
                'total' => \App\Models\DvdRom::count(),
                'available' => \App\Models\DvdRom::where('status', 'Available')->count(),
                'in_use' => \App\Models\DvdRom::where('status', 'In Use')->count(),
                'defective' => \App\Models\DvdRom::where('status', 'Defective')->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get repair history report
     */
    public function repairHistory(): JsonResponse
    {
        $repairs = MaintenanceRecord::with(['computer', 'computer.department'])
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $repairs->map(function ($repair) {
            return [
                'id' => $repair->id,
                'asset_tag' => $repair->asset_tag,
                'computer_name' => $repair->computer->computer_name ?? 'N/A',
                'department' => $repair->computer->department->name ?? 'N/A',
                'issue_description' => $repair->issue_description,
                'repair_action' => $repair->repair_action,
                'cost' => $repair->cost,
                'technician' => $repair->technician,
                'status' => $repair->status,
                'created_at' => $repair->created_at->format('Y-m-d H:i:s'),
                'completed_at' => $repair->completed_at?->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get deployment history
     */
    public function deploymentHistory(): JsonResponse
    {
        $deployments = Deployment::with(['computer', 'fromDepartment', 'toDepartment', 'fromLaboratory', 'toLaboratory'])
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $deployments->map(function ($deployment) {
            return [
                'id' => $deployment->id,
                'asset_tag' => $deployment->asset_tag,
                'computer_name' => $deployment->computer->computer_name ?? 'N/A',
                'transfer_type' => $deployment->transfer_type,
                'from_location' => $deployment->fromDepartment->name ?? $deployment->fromLaboratory->name ?? 'N/A',
                'to_location' => $deployment->toDepartment->name ?? $deployment->toLaboratory->name ?? 'N/A',
                'notes' => $deployment->notes,
                'created_at' => $deployment->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get dashboard statistics
     */
    public function dashboardStats(): JsonResponse
    {
        try {
            $data = [
                'overview' => [
                    'total_assets' => Asset::count(),
                    'total_computers' => Computer::count(),
                    'total_departments' => Department::count(),
                    'total_laboratories' => Laboratory::count(),
                ],
                'status_breakdown' => [
                    'available' => Computer::where('status', 'Working')->count(),
                    'deployed' => Computer::where('status', 'Working')->count(),
                    'under_repair' => Computer::where('status', 'Defective')->count(),
                    'defective' => Asset::where('status', 'Defective')->count(),
                ],
                'recent_activities' => $this->getRecentActivities(),
                'monthly_repairs' => $this->getMonthlyRepairStats(),
                'component_health' => $this->getComponentHealthStats(),
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching dashboard stats: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], 500);
        }
    }

    /**
     * Export report to CSV
     */
    public function exportReport(Request $request): JsonResponse
    {
        $type = $request->get('type', 'pcs_per_department');
        
        switch ($type) {
            case 'pcs_per_department':
                $data = $this->pcsPerDepartment()->getData()->data;
                $filename = 'pcs_per_department_' . date('Y-m-d') . '.csv';
                break;
            case 'pcs_per_laboratory':
                $data = $this->pcsPerLaboratory()->getData()->data;
                $filename = 'pcs_per_laboratory_' . date('Y-m-d') . '.csv';
                break;
            case 'component_usage':
                $data = $this->componentUsage()->getData()->data;
                $filename = 'component_usage_' . date('Y-m-d') . '.csv';
                break;
            case 'repair_history':
                $data = $this->repairHistory()->getData()->data;
                $filename = 'repair_history_' . date('Y-m-d') . '.csv';
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid report type'
                ]);
        }

        // For now, return the data. In production, you'd generate actual CSV files
        return response()->json([
            'success' => true,
            'message' => 'Report data ready for export',
            'data' => $data,
            'filename' => $filename
        ]);
    }

    private function getRecentActivities(): array
    {
        $activities = [];
        
        // Recent deployments
        $recentDeployments = Deployment::with('computer')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentDeployments as $deployment) {
            $activities[] = [
                'type' => 'deployment',
                'description' => "Computer {$deployment->asset_tag} deployed",
                'timestamp' => $deployment->created_at->diffForHumans(),
            ];
        }

        // Recent repairs
        $recentRepairs = MaintenanceRecord::with('computer')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentRepairs as $repair) {
            $activities[] = [
                'type' => 'repair',
                'description' => "Repair logged for {$repair->asset_tag}",
                'timestamp' => $repair->created_at->diffForHumans(),
            ];
        }

        return $activities;
    }

    private function getMonthlyRepairStats(): array
    {
        $repairs = MaintenanceRecord::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[] = [
                'month' => date('F', mktime(0, 0, 0, $i, 1)),
                'count' => $repairs[$i] ?? 0,
            ];
        }

        return $monthlyData;
    }

    private function getComponentHealthStats(): array
    {
        $components = [
            'processors' => \App\Models\Processor::class,
            'motherboards' => \App\Models\Motherboard::class,
            'rams' => \App\Models\Ram::class,
            'storages' => \App\Models\Storage::class,
            'video_cards' => \App\Models\VideoCard::class,
            'psus' => \App\Models\Psu::class,
            'dvd_roms' => \App\Models\DvdRom::class,
        ];

        $healthData = [];
        foreach ($components as $name => $model) {
            $total = $model::count();
            $defective = $model::where('status', 'Defective')->count();
            $healthPercentage = $total > 0 ? (($total - $defective) / $total) * 100 : 0;

            $healthData[] = [
                'component' => ucfirst(str_replace('_', ' ', $name)),
                'total' => $total,
                'defective' => $defective,
                'health_percentage' => round($healthPercentage, 2),
            ];
        }

        return $healthData;
    }
}
