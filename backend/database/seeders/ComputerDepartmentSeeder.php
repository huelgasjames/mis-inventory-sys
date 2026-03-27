<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\DepartmentCategory;

class ComputerDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the Technical category
        $technicalCategory = DepartmentCategory::where('name', 'Technical')->first();
        
        if (!$technicalCategory) {
            $this->command->error('Technical category not found. Please run DepartmentCategorySeeder first.');
            return;
        }

        // Create Computer Science Department
        $computerDept = Department::create([
            'name' => 'Computer Science Department',
            'description' => 'Department responsible for computer science education and IT infrastructure',
            'category_id' => $technicalCategory->id,
        ]);

        $this->command->info('Computer Science Department created successfully.');
    }
}
