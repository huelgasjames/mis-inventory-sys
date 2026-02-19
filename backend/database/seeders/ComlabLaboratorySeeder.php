<?php

namespace Database\Seeders;

use App\Models\Laboratory;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;

class ComlabLaboratorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create MISD department for Comlab laboratories
        $misdDepartment = Department::where('name', 'MISD')->first();
        
        if (!$misdDepartment) {
            // First ensure a category exists
            $category = \App\Models\DepartmentCategory::first();
            if (!$category) {
                $category = \App\Models\DepartmentCategory::create([
                    'name' => 'Academic',
                    'color' => '#0F6F43'
                ]);
            }
            
            $misdDepartment = Department::create([
                'name' => 'MISD',
                'description' => 'Management Information Systems Department',
                'category_id' => $category->id,
            ]);
        }

        // Get MISD department head or faculty
        $misdHead = User::where('role', 'department_head')
            ->where('name', 'like', '%MISD%')
            ->first();

        if (!$misdHead) {
            // Get any faculty user as fallback
            $misdHead = User::where('role', 'faculty')->first();
        }

        // Create Comlab laboratories CL-01 to CL-06
        $comlabLaboratories = [
            [
                'lab_code' => 'CL-01',
                'lab_name' => 'Computer Laboratory 01',
                'location' => 'BCH',
                'building' => 'Room 201',
                'floor' => '2nd Floor',
                'capacity' => 30,
                'department_id' => $misdDepartment->id,
                'head_of_lab' => $misdHead?->id,
                'contact_number' => $misdHead?->phone_number ?? 'N/A',
                'email' => $misdHead?->email ?? 'comlab01@university.edu',
                'is_active' => true,
            ],
            [
                'lab_code' => 'CL-02',
                'lab_name' => 'Computer Laboratory 02',
                'location' => 'BCH',
                'building' => 'Room 202',
                'floor' => '2nd Floor',
                'capacity' => 30,
                'department_id' => $misdDepartment->id,
                'head_of_lab' => $misdHead?->id,
                'contact_number' => $misdHead?->phone_number ?? 'N/A',
                'email' => $misdHead?->email ?? 'comlab02@university.edu',
                'is_active' => true,
            ],
            [
                'lab_code' => 'CL-03',
                'lab_name' => 'Computer Laboratory 03',
                'location' => 'BCH',
                'building' => 'Room 203',
                'floor' => '2nd Floor',
                'capacity' => 25,
                'department_id' => $misdDepartment->id,
                'head_of_lab' => $misdHead?->id,
                'contact_number' => $misdHead?->phone_number ?? 'N/A',
                'email' => $misdHead?->email ?? 'comlab03@university.edu',
                'is_active' => true,
            ],
            [
                'lab_code' => 'CL-04',
                'lab_name' => 'Computer Laboratory 04',
                'location' => 'BCH',
                'building' => 'Room 204',
                'floor' => '2nd Floor',
                'capacity' => 25,
                'department_id' => $misdDepartment->id,
                'head_of_lab' => $misdHead?->id,
                'contact_number' => $misdHead?->phone_number ?? 'N/A',
                'email' => $misdHead?->email ?? 'comlab04@university.edu',
                'is_active' => true,
            ],
            [
                'lab_code' => 'CL-05',
                'lab_name' => 'Computer Laboratory 05',
                'location' => 'BCH',
                'building' => 'Room 205',
                'floor' => '2nd Floor',
                'capacity' => 35,
                'department_id' => $misdDepartment->id,
                'head_of_lab' => $misdHead?->id,
                'contact_number' => $misdHead?->phone_number ?? 'N/A',
                'email' => $misdHead?->email ?? 'comlab05@university.edu',
                'is_active' => true,
            ],
            [
                'lab_code' => 'CL-06',
                'lab_name' => 'Computer Laboratory 06',
                'location' => 'BCH',
                'building' => 'Room 206',
                'floor' => '2nd Floor',
                'capacity' => 35,
                'department_id' => $misdDepartment->id,
                'head_of_lab' => $misdHead?->id,
                'contact_number' => $misdHead?->phone_number ?? 'N/A',
                'email' => $misdHead?->email ?? 'comlab06@university.edu',
                'is_active' => true,
            ],
        ];

        foreach ($comlabLaboratories as $lab) {
            // Check if laboratory already exists
            $existingLab = Laboratory::where('lab_code', $lab['lab_code'])->first();
            
            if (!$existingLab) {
                Laboratory::create($lab);
                $this->command->info("Created laboratory: {$lab['lab_code']} - {$lab['lab_name']}");
            } else {
                $this->command->info("Laboratory already exists: {$lab['lab_code']} - {$lab['lab_name']}");
            }
        }
    }
}
