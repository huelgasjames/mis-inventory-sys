<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asset;
use App\Models\Computer;
use App\Models\Department;
use App\Models\DepartmentCategory;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get category IDs
        $academicCategory = DepartmentCategory::where('name', 'Academic')->first();
        $adminCategory = DepartmentCategory::where('name', 'Administrative')->first();
        
        if (!$academicCategory || !$adminCategory) {
            $this->command->error('Department categories not found. Please run DepartmentCategorySeeder first.');
            return;
        }
        
        // Create departments with categories
        $departments = [
            ['name' => 'Computer Science', 'description' => 'Computer Science Department', 'category_id' => $academicCategory->id],
            ['name' => 'Administration', 'description' => 'Administration Office', 'category_id' => $adminCategory->id],
            ['name' => 'Library', 'description' => 'University Library', 'category_id' => $academicCategory->id],
            ['name' => 'Accounting', 'description' => 'Accounting Department', 'category_id' => $academicCategory->id],
            ['name' => 'Engineering', 'description' => 'Engineering Department', 'category_id' => $academicCategory->id],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['name' => $dept['name']], $dept);
        }

        // Get department IDs for proper relationships
        $csDept = Department::where('name', 'Computer Science')->first();
        $adminDept = Department::where('name', 'Administration')->first();
        $libraryDept = Department::where('name', 'Library')->first();
        $accountingDept = Department::where('name', 'Accounting')->first();
        $engineeringDept = Department::where('name', 'Engineering')->first();

        // Create sample assets
        $assets = [
            [
                'asset_tag' => 'PC001',
                'computer_name' => 'CS Lab Computer 1',
                'status' => 'Working',
                'department_id' => $csDept->id,
                'assigned_to' => 1,
                'description' => 'Desktop computer for Computer Science Lab'
            ],
            [
                'asset_tag' => 'PC002',
                'computer_name' => 'CS Lab Computer 2',
                'status' => 'Working',
                'department_id' => $csDept->id,
                'assigned_to' => 2,
                'description' => 'Desktop computer for Computer Science Lab'
            ],
            [
                'asset_tag' => 'PC003',
                'computer_name' => 'Admin Office PC',
                'status' => 'Working',
                'department_id' => $adminDept->id,
                'assigned_to' => 1,
                'description' => 'Desktop computer for Administration Office'
            ],
            [
                'asset_tag' => 'PC004',
                'computer_name' => 'Library PC 1',
                'status' => 'Defective',
                'department_id' => $libraryDept->id,
                'assigned_to' => null,
                'description' => 'Desktop computer for Library - needs repair'
            ],
            [
                'asset_tag' => 'PC005',
                'computer_name' => 'Library PC 2',
                'status' => 'Working',
                'department_id' => $libraryDept->id,
                'assigned_to' => 3,
                'description' => 'Desktop computer for Library'
            ],
            [
                'asset_tag' => 'LAP001',
                'computer_name' => 'Engineering Laptop 1',
                'status' => 'Working',
                'department_id' => $engineeringDept->id,
                'assigned_to' => 2,
                'description' => 'Laptop for Engineering Department'
            ],
        ];

        foreach ($assets as $asset) {
            Asset::firstOrCreate(['asset_tag' => $asset['asset_tag']], $asset);
        }

        // Create sample computers
        $computers = [
            [
                'computer_name' => 'CS-LAB-01',
                'asset_tag' => 'PC001',
                'processor' => 'Intel Core i5-10400',
                'motherboard' => 'ASUS Prime H410M',
                'ram' => '16GB DDR4',
                'storage' => '512GB SSD',
                'video_card' => 'NVIDIA GTX 1650',
                'psu' => '450W',
                'dvd_rom' => 'DVD-RW',
                'status' => 'Working',
                'department_id' => $csDept->id,
                'assigned_to' => 1,
            ],
            [
                'computer_name' => 'CS-LAB-02',
                'asset_tag' => 'PC002',
                'processor' => 'Intel Core i5-10400',
                'motherboard' => 'ASUS Prime H410M',
                'ram' => '16GB DDR4',
                'storage' => '512GB SSD',
                'video_card' => 'NVIDIA GTX 1650',
                'psu' => '450W',
                'dvd_rom' => 'DVD-RW',
                'status' => 'Working',
                'department_id' => $csDept->id,
                'assigned_to' => 2,
            ],
            [
                'computer_name' => 'ADMIN-PC-01',
                'asset_tag' => 'PC003',
                'processor' => 'Intel Core i3-10100',
                'motherboard' => 'Gigabyte H410M',
                'ram' => '8GB DDR4',
                'storage' => '256GB SSD',
                'video_card' => 'Integrated',
                'psu' => '350W',
                'dvd_rom' => null,
                'status' => 'Working',
                'department_id' => $adminDept->id,
                'assigned_to' => 1,
            ],
        ];

        foreach ($computers as $computer) {
            Computer::firstOrCreate(['asset_tag' => $computer['asset_tag']], $computer);
        }
    }
}
