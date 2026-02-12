<?php

namespace Database\Seeders;

use App\Models\Laboratory;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;

class LaboratorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create departments for reference
        $departments = Department::pluck('id', 'name')->toArray();
        
        // Get or create users for lab heads
        $labHeads = User::where('role', 'faculty')->pluck('id', 'name')->toArray();

        $laboratories = [
            [
                'lab_code' => 'CS-LAB-001',
                'lab_name' => 'Computer Science Laboratory 1',
                'location' => 'BCH',
                'building' => 'Room 101',
                'floor' => '2nd Floor',
                'capacity' => 30,
                'department_id' => $departments['Computer Science'] ?? null,
                'head_of_lab' => $labHeads['Dr. Smith'] ?? null,
                'is_active' => true,
            ],
            [
                'lab_code' => 'CS-LAB-002',
                'lab_name' => 'Computer Science Laboratory 2',
                'location' => 'BCH',
                'building' => 'Room 102',
                'floor' => '3rd Floor',
                'capacity' => 25,
                'department_id' => $departments['Computer Science'] ?? null,
                'head_of_lab' => $labHeads['Prof. Johnson'] ?? null,
                'is_active' => true,
            ],
            [
                'lab_code' => 'IT-LAB-001',
                'lab_name' => 'Information Technology Laboratory',
                'location' => 'UC',
                'building' => 'Lab-A1',
                'floor' => '1st Floor',
                'capacity' => 35,
                'department_id' => $departments['Information Technology'] ?? null,
                'head_of_lab' => $labHeads['Dr. Williams'] ?? null,
                'is_active' => true,
            ],
            [
                'lab_code' => 'ENG-LAB-001',
                'lab_name' => 'Engineering Laboratory',
                'location' => 'UC',
                'building' => 'Room 201',
                'floor' => '1st Floor',
                'capacity' => 40,
                'department_id' => $departments['Engineering'] ?? null,
                'head_of_lab' => $labHeads['Prof. Brown'] ?? null,
                'is_active' => true,
            ],
            [
                'lab_code' => 'MATH-LAB-001',
                'lab_name' => 'Mathematics Laboratory',
                'location' => 'BCH',
                'building' => 'Room 301',
                'floor' => '2nd Floor',
                'capacity' => 20,
                'department_id' => $departments['Mathematics'] ?? null,
                'head_of_lab' => $labHeads['Dr. Davis'] ?? null,
                'is_active' => true,
            ],
            [
                'lab_code' => 'PHYS-LAB-001',
                'lab_name' => 'Physics Laboratory',
                'location' => 'BCH',
                'building' => 'Room 302',
                'floor' => '3rd Floor',
                'capacity' => 25,
                'department_id' => $departments['Physics'] ?? null,
                'head_of_lab' => $labHeads['Prof. Miller'] ?? null,
                'is_active' => true,
            ],
            [
                'lab_code' => 'CHEM-LAB-001',
                'lab_name' => 'Chemistry Laboratory',
                'location' => 'UC',
                'building' => 'Room 401',
                'floor' => '4th Floor',
                'capacity' => 30,
                'department_id' => $departments['Chemistry'] ?? null,
                'head_of_lab' => $labHeads['Dr. Wilson'] ?? null,
                'is_active' => true,
            ],
            [
                'lab_code' => 'LIB-LAB-001',
                'lab_name' => 'Library Computer Laboratory',
                'location' => 'UC',
                'building' => 'Lab-B1',
                'floor' => '2nd Floor',
                'capacity' => 50,
                'department_id' => $departments['Library'] ?? null,
                'head_of_lab' => $labHeads['Ms. Taylor'] ?? null,
                'is_active' => true,
            ],
        ];

        foreach ($laboratories as $lab) {
            Laboratory::create($lab);
        }
    }
}
