<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Laboratory;
use App\Models\Computer;
use App\Models\Deployment;
use App\Models\MaintenanceRecord;
use App\Models\User;

class DashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create departments
        $departments = [
            ['name' => 'Computer Laboratory', 'description' => 'Main computer laboratory - Comlab'],
            ['name' => 'Library', 'description' => 'Library computer section'],
            ['name' => 'Office', 'description' => 'Administrative office'],
            ['name' => 'Classroom 1', 'description' => 'First classroom'],
            ['name' => 'Classroom 2', 'description' => 'Second classroom'],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }

        // Create laboratories with Comlab specific locations
        $labs = [
            ['name' => 'Comlab Main', 'description' => 'Main Computer Laboratory'],
            ['name' => 'Comlab Annex', 'description' => 'Computer Laboratory Annex'],
            ['name' => 'Comlab Research', 'description' => 'Computer Laboratory Research Area'],
        ];

        foreach ($labs as $lab) {
            Laboratory::create($lab);
        }

        // Create sample computers
        $statuses = ['Available', 'Deployed', 'Under Repair'];
        for ($i = 1; $i <= 20; $i++) {
            Computer::create([
                'computer_name' => 'PC-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'asset_tag' => 'AST-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'pc_number' => 'PC' . $i,
                'serial_number' => 'SN' . rand(100000, 999999),
                'department_id' => 1, // Computer Laboratory department
                'laboratory_id' => rand(1, 3), // Comlab locations
                'status' => $statuses[array_rand($statuses)],
                'assigned_to' => 'User ' . $i,
                'purchase_date' => now()->subDays(rand(30, 365)),
                'location' => 'Comlab - ' . ['Main', 'Annex', 'Research'][rand(0, 2)],
            ]);
        }

        // Create sample deployments specifically for Comlab locations
        $comlabLocations = [
            ['lab_id' => 1, 'location' => 'Comlab Main - Room 101'],
            ['lab_id' => 1, 'location' => 'Comlab Main - Room 102'],
            ['lab_id' => 2, 'location' => 'Comlab Annex - Room 201'],
            ['lab_id' => 2, 'location' => 'Comlab Annex - Room 202'],
            ['lab_id' => 3, 'location' => 'Comlab Research - Room 301'],
        ];

        for ($i = 1; $i <= 10; $i++) {
            $location = $comlabLocations[array_rand($comlabLocations)];
            Deployment::create([
                'computer_id' => $i,
                'department_id' => 1, // Computer Laboratory department
                'laboratory_id' => $location['lab_id'],
                'deployed_by' => 1, // Assuming user ID 1 exists
                'deployment_date' => now()->subDays(rand(1, 30)),
                'status' => 'Active',
                'notes' => 'Deployed to ' . $location['location'],
            ]);
        }

        // Create sample maintenance records
        $issueTypes = ['Hardware Failure', 'Software Issue', 'Network Problem', 'Power Supply'];
        for ($i = 1; $i <= 8; $i++) {
            MaintenanceRecord::create([
                'computer_id' => rand(1, 20),
                'issue_type' => $issueTypes[array_rand($issueTypes)],
                'description' => 'Sample maintenance issue for computer ' . $i,
                'status' => 'Completed',
                'reported_date' => now()->subDays(rand(1, 60)),
                'resolved_date' => now()->subDays(rand(0, 30)),
                'cost' => rand(50, 500),
                'technician_id' => 1,
            ]);
        }
    }
}
