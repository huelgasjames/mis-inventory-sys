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
            'code' => 'CS',
            'description' => 'Department responsible for computer science education and IT infrastructure',
            'head_of_department' => null, // Will be assigned later
            'location' => 'Main Building',
            'contact_number' => '123-456-7890',
            'email' => 'cs.department@university.edu',
            'is_active' => true,
            'category_id' => $technicalCategory->id,
        ]);

        $this->command->info('Computer Science Department created successfully.');
    }
}
