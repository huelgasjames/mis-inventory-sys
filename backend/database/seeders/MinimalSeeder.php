<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\Laboratory;
use App\Models\Computer;
use App\Models\Deployment;
use App\Models\MaintenanceRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class MinimalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        MaintenanceRecord::truncate();
        Deployment::truncate();
        Computer::truncate();
        Laboratory::truncate();
        Department::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@university.edu'],
            [
                'user_id' => 'U001',
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'address' => 'University Campus, Main Building',
                'birthdate' => '1990-01-15',
                'contact_person' => 'IT Department',
                'contact_number' => '123-456-7890',
                'is_active' => true,
            ]
        );

        $technician = User::firstOrCreate(
            ['email' => 'tech@university.edu'],
            [
                'user_id' => 'U002',
                'name' => 'John Technician',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'address' => 'University Campus, IT Building',
                'birthdate' => '1992-05-20',
                'contact_person' => 'IT Department',
                'contact_number' => '123-456-7891',
                'is_active' => true,
            ]
        );

        // Create Departments
        $departments = [
            ['name' => 'Computer Laboratory', 'description' => 'Main computer laboratory for student use'],
            ['name' => 'Library', 'description' => 'Library computer section for research'],
            ['name' => 'IT Department', 'description' => 'Information Technology department'],
            ['name' => 'Accounting Office', 'description' => 'Financial and accounting office'],
            ['name' => 'Registrar Office', 'description' => 'Student registration office'],
            ['name' => 'Faculty Room', 'description' => 'Faculty office computers'],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }

        // Create Laboratories
        $laboratories = [
            [
                'department_id' => 1,
                'lab_code' => 'LAB-A',
                'lab_name' => 'Laboratory A - Programming',
                'location' => 'Building 1, Room 101',
                'building' => 'IT Building',
                'floor' => 1,
                'capacity' => 30,
                'head_of_lab' => 'Prof. John Smith',
                'contact_number' => '123-456-7892',
                'email' => 'lab-a@university.edu',
                'is_active' => true,
            ],
            [
                'department_id' => 1,
                'lab_code' => 'LAB-B',
                'lab_name' => 'Laboratory B - Multimedia',
                'location' => 'Building 2, Room 201',
                'building' => 'Multimedia Building',
                'floor' => 2,
                'capacity' => 25,
                'head_of_lab' => 'Prof. Jane Doe',
                'contact_number' => '123-456-7893',
                'email' => 'lab-b@university.edu',
                'is_active' => true,
            ],
            [
                'department_id' => 1,
                'lab_code' => 'LAB-C',
                'lab_name' => 'Laboratory C - Research',
                'location' => 'Building 3, Room 301',
                'building' => 'Research Building',
                'floor' => 3,
                'capacity' => 20,
                'head_of_lab' => 'Prof. Robert Johnson',
                'contact_number' => '123-456-7894',
                'email' => 'lab-c@university.edu',
                'is_active' => true,
            ],
        ];

        foreach ($laboratories as $lab) {
            Laboratory::create($lab);
        }

        // Create Computers (without components for now)
        $computers = [];
        for ($i = 1; $i <= 30; $i++) {
            $status = ['Available', 'Deployed', 'Under Repair'][array_rand(['Available', 'Deployed', 'Under Repair'])];
            
            $computer = Computer::create([
                'computer_name' => 'PC-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'asset_tag' => 'UOC-' . date('Y') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'pc_number' => 'PC' . $i,
                'serial_number' => 'SN' . strtoupper(uniqid()) . rand(1000, 9999),
                'department_id' => rand(1, 6),
                'laboratory_id' => rand(1, 3),
                'status' => $status,
                'assigned_to' => $status === 'Deployed' ? 'Employee ' . $i : null,
                'purchase_date' => now()->subDays(rand(30, 730)),
                'warranty_expiry' => now()->addDays(rand(365, 1095)),
            ]);
            
            $computers[] = $computer;
        }

        // Create Deployments
        $deploymentCount = 0;
        foreach ($computers as $computer) {
            if ($computer->status === 'Deployed' && $deploymentCount < 18) {
                Deployment::create([
                    'computer_id' => $computer->id,
                    'department_id' => $computer->department_id,
                    'laboratory_id' => $computer->laboratory_id,
                    'deployed_by' => $admin->id,
                    'deployment_date' => now()->subDays(rand(1, 90)),
                    'expected_return_date' => now()->addDays(rand(30, 180)),
                    'status' => 'Active',
                    'notes' => 'Deployed for regular use',
                ]);
                $deploymentCount++;
            }
        }

        // Create Maintenance Records
        $issueTypes = [
            'Hardware Failure',
            'Software Issue',
            'Network Problem',
            'Power Supply Issue',
            'Overheating',
            'Memory Failure',
            'Storage Issue',
            'Display Problem'
        ];

        for ($i = 1; $i <= 25; $i++) {
            $computer = $computers[array_rand($computers)];
            $reportedDate = now()->subDays(rand(1, 180));
            $resolvedDate = $reportedDate->copy()->addDays(rand(1, 30));
            
            MaintenanceRecord::create([
                'computer_id' => $computer->id,
                'issue_type' => $issueTypes[array_rand($issueTypes)],
                'description' => "Maintenance issue reported for {$computer->computer_name}: " . $issueTypes[array_rand($issueTypes)],
                'status' => 'Completed',
                'reported_date' => $reportedDate,
                'resolved_date' => $resolvedDate,
                'cost' => rand(50, 800),
                'technician_id' => $technician->id,
                'notes' => 'Issue resolved successfully',
            ]);
        }

        // Create some pending maintenance records
        for ($i = 1; $i <= 8; $i++) {
            $computer = $computers[array_rand($computers)];
            
            MaintenanceRecord::create([
                'computer_id' => $computer->id,
                'issue_type' => $issueTypes[array_rand($issueTypes)],
                'description' => "Pending maintenance for {$computer->computer_name}",
                'status' => 'Pending',
                'reported_date' => now()->subDays(rand(1, 7)),
                'resolved_date' => null,
                'cost' => null,
                'technician_id' => null,
                'notes' => 'Waiting for technician assignment',
            ]);
        }

        $this->command->info('MinimalSeeder completed successfully!');
        $this->command->info('Created:');
        $this->command->info('- 2 Users (Admin, Technician)');
        $this->command->info('- 6 Departments');
        $this->command->info('- 3 Laboratories');
        $this->command->info('- 30 Computers');
        $this->command->info('- 18 Active Deployments');
        $this->command->info('- 33 Maintenance Records (25 completed, 8 pending)');
        $this->command->info('');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@university.edu / password');
        $this->command->info('Technician: tech@university.edu / password');
    }
}
