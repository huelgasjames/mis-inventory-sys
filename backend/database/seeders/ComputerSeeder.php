<?php

namespace Database\Seeders;

use App\Models\Computer;
use App\Models\Department;
use Illuminate\Database\Seeder;

class ComputerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get departments for reference
        $departments = Department::pluck('id', 'name')->toArray();

        $computers = [
            [
                'computer_name' => 'CS-LAB-001-PC-01',
                'asset_tag' => 'PC-001',
                'pc_number' => 'PC001',
                'serial_number' => 'SN001234567',
                'department_id' => $departments['Computer Science'] ?? 1,
                'status' => 'Working',
                'location' => 'Computer Science Laboratory 1',
            ],
            [
                'computer_name' => 'CS-LAB-001-PC-02',
                'asset_tag' => 'PC-002',
                'pc_number' => 'PC002',
                'serial_number' => 'SN001234568',
                'department_id' => $departments['Computer Science'] ?? 1,
                'status' => 'Working',
                'location' => 'Computer Science Laboratory 1',
            ],
            [
                'computer_name' => 'CS-LAB-002-PC-01',
                'asset_tag' => 'PC-003',
                'pc_number' => 'PC003',
                'serial_number' => 'SN001234569',
                'department_id' => $departments['Computer Science'] ?? 1,
                'status' => 'Working',
                'location' => 'Computer Science Laboratory 2',
            ],
            [
                'computer_name' => 'IT-LAB-001-PC-01',
                'asset_tag' => 'PC-004',
                'pc_number' => 'PC004',
                'serial_number' => 'SN001234570',
                'department_id' => $departments['Information Technology'] ?? 2,
                'status' => 'Working',
                'location' => 'Information Technology Laboratory',
            ],
            [
                'computer_name' => 'IT-LAB-001-PC-02',
                'asset_tag' => 'PC-005',
                'pc_number' => 'PC005',
                'serial_number' => 'SN001234571',
                'department_id' => $departments['Information Technology'] ?? 2,
                'status' => 'Defective',
                'location' => 'Information Technology Laboratory',
            ],
            [
                'computer_name' => 'ENG-LAB-001-PC-01',
                'asset_tag' => 'PC-006',
                'pc_number' => 'PC006',
                'serial_number' => 'SN001234572',
                'department_id' => $departments['Engineering'] ?? 3,
                'status' => 'Working',
                'location' => 'Engineering Laboratory',
            ],
            [
                'computer_name' => 'ENG-LAB-001-PC-02',
                'asset_tag' => 'PC-007',
                'pc_number' => 'PC007',
                'serial_number' => 'SN001234573',
                'department_id' => $departments['Engineering'] ?? 3,
                'status' => 'Under Repair',
                'location' => 'Engineering Laboratory',
            ],
            [
                'computer_name' => 'LIB-LAB-001-PC-01',
                'asset_tag' => 'PC-008',
                'pc_number' => 'PC008',
                'serial_number' => 'SN001234574',
                'department_id' => $departments['Library'] ?? 7,
                'status' => 'Working',
                'location' => 'Library Computer Laboratory',
            ],
            [
                'computer_name' => 'LIB-LAB-001-PC-02',
                'asset_tag' => 'PC-009',
                'pc_number' => 'PC009',
                'serial_number' => 'SN001234575',
                'department_id' => $departments['Library'] ?? 7,
                'status' => 'Working',
                'location' => 'Library Computer Laboratory',
            ],
            [
                'computer_name' => 'LIB-LAB-001-PC-03',
                'asset_tag' => 'PC-010',
                'pc_number' => 'PC010',
                'serial_number' => 'SN001234576',
                'department_id' => $departments['Library'] ?? 7,
                'status' => 'For Disposal',
                'location' => 'Library Computer Laboratory',
            ],
        ];

        foreach ($computers as $computer) {
            Computer::create($computer);
        }
    }
}
