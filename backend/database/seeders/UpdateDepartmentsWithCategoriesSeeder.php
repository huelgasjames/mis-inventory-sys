<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\DepartmentCategory;
use Illuminate\Database\Seeder;

class UpdateDepartmentsWithCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the category IDs
        $academicUseCategory = DepartmentCategory::where('name', 'Academic Use')->first();
        $inventoryCategory = DepartmentCategory::where('name', 'Inventory')->first();
        $forCondemnCategory = DepartmentCategory::where('name', 'For Condemn')->first();

        // Update departments to use the correct categories
        if ($academicUseCategory) {
            Department::where('name', 'Academic Use')->update(['category_id' => $academicUseCategory->id]);
        }
        
        if ($inventoryCategory) {
            Department::where('name', 'Inventory')->update(['category_id' => $inventoryCategory->id]);
        }
        
        if ($forCondemnCategory) {
            Department::where('name', 'For Condemn')->update(['category_id' => $forCondemnCategory->id]);
        }
    }
}
