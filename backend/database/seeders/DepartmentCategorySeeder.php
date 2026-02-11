<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DepartmentCategory;

class DepartmentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Academic',
                'description' => 'Academic departments focused on teaching and research',
                'color' => '#0F6F43',
            ],
            [
                'name' => 'Administrative',
                'description' => 'Administrative and support departments',
                'color' => '#004D7A',
            ],
            [
                'name' => 'Technical',
                'description' => 'Technical and IT-related departments',
                'color' => '#FF6B35',
            ],
            [
                'name' => 'Student Services',
                'description' => 'Departments providing student support services',
                'color' => '#8B5CF6',
            ],
            [
                'name' => 'Facilities',
                'description' => 'Facilities management and maintenance departments',
                'color' => '#10B981',
            ],
        ];

        foreach ($categories as $category) {
            DepartmentCategory::create($category);
        }
    }
}
