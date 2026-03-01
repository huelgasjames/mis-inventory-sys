<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class SpecificDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Academic Use', 'description' => 'Department for academic use of equipment and facilities'],
            ['name' => 'Inventory', 'description' => 'Department for inventory management and storage'],
            ['name' => 'For Condemn', 'description' => 'Department for items scheduled for condemnation'],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(
                ['name' => $dept['name']],
                ['description' => $dept['description']]
            );
        }
    }
}
