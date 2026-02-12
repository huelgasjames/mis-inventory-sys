<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Computer Science', 'description' => 'Department of Computer Science'],
            ['name' => 'Information Technology', 'description' => 'Department of Information Technology'],
            ['name' => 'Engineering', 'description' => 'Department of Engineering'],
            ['name' => 'Mathematics', 'description' => 'Department of Mathematics'],
            ['name' => 'Physics', 'description' => 'Department of Physics'],
            ['name' => 'Chemistry', 'description' => 'Department of Chemistry'],
            ['name' => 'Library', 'description' => 'Main Library'],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }
    }
}
