<?php

namespace Database\Seeders;

use App\Models\DepartmentCategory;
use Illuminate\Database\Seeder;

class SpecificDepartmentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing categories to avoid confusion
        DepartmentCategory::query()->delete();
        
        $categories = [
            [
                'name' => 'Academic Use',
                'description' => 'Items designated for academic and educational purposes',
                'color' => '#0F6F43',
            ],
            [
                'name' => 'Inventory',
                'description' => 'Items in inventory storage and management',
                'color' => '#004D7A',
            ],
            [
                'name' => 'For Condemn',
                'description' => 'Items scheduled for condemnation and disposal',
                'color' => '#FF6B35',
            ],
        ];

        foreach ($categories as $category) {
            DepartmentCategory::updateOrCreate(
                ['name' => $category['name']],
                [
                    'description' => $category['description'],
                    'color' => $category['color']
                ]
            );
        }
    }
}
