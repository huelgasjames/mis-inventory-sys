<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Laboratory;
use App\Models\Department;

class ComputerLaboratorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the Computer Science Department
        $computerDept = Department::where('name', 'Computer Science Department')->first();
        
        if (!$computerDept) {
            $this->command->error('Computer Science Department not found. Please run ComputerDepartmentSeeder first.');
            return;
        }

        // Create Computer Laboratory
        Laboratory::create([
            'department_id' => $computerDept->id,
            'lab_code' => 'COMPLAB001',
            'lab_name' => 'Computer Laboratory',
            'location' => 'Main Building, Room 101',
            'building' => 'Main Building',
            'floor' => '1st Floor',
            'capacity' => 30,
            'head_of_lab' => null, // Will be assigned later
            'contact_number' => '123-456-7891',
            'email' => 'complab@university.edu',
            'is_active' => true,
        ]);

        $this->command->info('Computer Laboratory created successfully.');
    }
}
