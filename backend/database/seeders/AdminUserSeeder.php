<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::updateOrCreate(
            ['user_id' => '2001746'],
            [
                'name' => 'System Administrator',
                'email' => 'admin@pnc.edu.ph',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create test student user
        User::updateOrCreate(
            ['user_id' => '2001747'],
            [
                'name' => 'Test Student',
                'email' => 'student@pnc.edu.ph',
                'password' => Hash::make('student123'),
                'role' => 'student',
                'email_verified_at' => now(),
            ]
        );

        // Create regular user
        User::updateOrCreate(
            ['user_id' => '2001748'],
            [
                'name' => 'Regular User',
                'email' => 'user@pnc.edu.ph',
                'password' => Hash::make('user123'),
                'role' => 'staff',
                'email_verified_at' => now(),
            ]
        );
    }
}
