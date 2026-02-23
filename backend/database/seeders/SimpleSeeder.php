<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\Laboratory;
use App\Models\Computer;
use App\Models\Processor;
use App\Models\Motherboard;
use App\Models\Ram;
use App\Models\Storage;
use App\Models\VideoCard;
use App\Models\Psu;
use App\Models\DvdRom;
use App\Models\Deployment;
use App\Models\MaintenanceRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SimpleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        MaintenanceRecord::truncate();
        Deployment::truncate();
        Computer::truncate();
        DvdRom::truncate();
        Psu::truncate();
        VideoCard::truncate();
        Storage::truncate();
        Ram::truncate();
        Motherboard::truncate();
        Processor::truncate();
        Laboratory::truncate();
        Department::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@university.edu'],
            [
                'user_id' => 'U001',
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'address' => 'University Campus, Main Building',
                'birthdate' => '1990-01-15',
                'contact_person' => 'IT Department',
                'contact_number' => '123-456-7890',
                'is_active' => true,
            ]
        );

        $technician = User::firstOrCreate(
            ['email' => 'tech@university.edu'],
            [
                'user_id' => 'U002',
                'name' => 'John Technician',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'address' => 'University Campus, IT Building',
                'birthdate' => '1992-05-20',
                'contact_person' => 'IT Department',
                'contact_number' => '123-456-7891',
                'is_active' => true,
            ]
        );

        // Create Departments
        $departments = [
            ['name' => 'Computer Laboratory', 'description' => 'Main computer laboratory for student use'],
            ['name' => 'Library', 'description' => 'Library computer section for research'],
            ['name' => 'IT Department', 'description' => 'Information Technology department'],
            ['name' => 'Accounting Office', 'description' => 'Financial and accounting office'],
            ['name' => 'Registrar Office', 'description' => 'Student registration office'],
            ['name' => 'Faculty Room', 'description' => 'Faculty office computers'],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }

        // Create Laboratories
        $laboratories = [
            [
                'department_id' => 1,
                'lab_code' => 'LAB-A',
                'lab_name' => 'Laboratory A - Programming',
                'location' => 'Building 1, Room 101',
                'building' => 'IT Building',
                'floor' => 1,
                'capacity' => 30,
                'head_of_lab' => 'Prof. John Smith',
                'contact_number' => '123-456-7892',
                'email' => 'lab-a@university.edu',
                'is_active' => true,
            ],
            [
                'department_id' => 1,
                'lab_code' => 'LAB-B',
                'lab_name' => 'Laboratory B - Multimedia',
                'location' => 'Building 2, Room 201',
                'building' => 'Multimedia Building',
                'floor' => 2,
                'capacity' => 25,
                'head_of_lab' => 'Prof. Jane Doe',
                'contact_number' => '123-456-7893',
                'email' => 'lab-b@university.edu',
                'is_active' => true,
            ],
            [
                'department_id' => 1,
                'lab_code' => 'LAB-C',
                'lab_name' => 'Laboratory C - Research',
                'location' => 'Building 3, Room 301',
                'building' => 'Research Building',
                'floor' => 3,
                'capacity' => 20,
                'head_of_lab' => 'Prof. Robert Johnson',
                'contact_number' => '123-456-7894',
                'email' => 'lab-c@university.edu',
                'is_active' => true,
            ],
        ];

        foreach ($laboratories as $lab) {
            Laboratory::create($lab);
        }

        // Create Computer Components (using only basic fields)
        $processors = [
            ['model' => 'Intel Core i7-10700K', 'status' => 1],
            ['model' => 'AMD Ryzen 9 5900X', 'status' => 1],
            ['model' => 'Intel Core i5-11600K', 'status' => 1],
            ['model' => 'AMD Ryzen 7 5800X', 'status' => 2],
            ['model' => 'Intel Core i3-10100', 'status' => 1],
        ];

        foreach ($processors as $processor) {
            Processor::create($processor);
        }

        $motherboards = [
            ['model' => 'ASUS ROG STRIX Z590-E', 'status' => 1],
            ['model' => 'MSI MPG X570 GAMING PLUS', 'status' => 1],
            ['model' => 'GIGABYTE B550 AORUS PRO', 'status' => 1],
            ['model' => 'ASRock B450M Steel Legend', 'status' => 3],
            ['model' => 'ASUS PRIME H510M', 'status' => 1],
        ];

        foreach ($motherboards as $motherboard) {
            Motherboard::create($motherboard);
        }

        $rams = [
            ['capacity' => '16GB', 'type' => 'DDR4', 'speed' => '3200MHz', 'status' => 1],
            ['capacity' => '32GB', 'type' => 'DDR4', 'speed' => '3200MHz', 'status' => 1],
            ['capacity' => '8GB', 'type' => 'DDR4', 'speed' => '2666MHz', 'status' => 1],
            ['capacity' => '64GB', 'type' => 'DDR4', 'speed' => '3600MHz', 'status' => 1],
            ['capacity' => '16GB', 'type' => 'DDR4', 'speed' => '2400MHz', 'status' => 2],
        ];

        foreach ($rams as $ram) {
            Ram::create($ram);
        }

        $storages = [
            ['capacity' => '512GB', 'type' => 'NVMe SSD', 'brand' => 'Samsung', 'status' => 1],
            ['capacity' => '1TB', 'type' => 'NVMe SSD', 'brand' => 'Western Digital', 'status' => 1],
            ['capacity' => '2TB', 'type' => 'SATA SSD', 'brand' => 'Crucial', 'status' => 1],
            ['capacity' => '4TB', 'type' => 'HDD', 'brand' => 'Seagate', 'status' => 1],
            ['capacity' => '256GB', 'type' => 'SATA SSD', 'brand' => 'Kingston', 'status' => 3],
        ];

        foreach ($storages as $storage) {
            Storage::create($storage);
        }

        $videoCards = [
            ['model' => 'NVIDIA GeForce RTX 3070', 'memory' => '8GB GDDR6', 'status' => 1],
            ['model' => 'AMD Radeon RX 6700 XT', 'memory' => '12GB GDDR6', 'status' => 1],
            ['model' => 'NVIDIA GeForce GTX 1660', 'memory' => '6GB GDDR5', 'status' => 1],
            ['model' => 'AMD Radeon RX 580', 'memory' => '8GB GDDR5', 'status' => 2],
            ['model' => 'Intel UHD Graphics 730', 'memory' => '4GB', 'status' => 1],
        ];

        foreach ($videoCards as $videoCard) {
            VideoCard::create($videoCard);
        }

        $psus = [
            ['model' => 'Corsair RM750X', 'wattage' => '750W', 'efficiency' => '80+ Gold', 'status' => 1],
            ['model' => 'Seasonic Focus GX-650', 'wattage' => '650W', 'efficiency' => '80+ Gold', 'status' => 1],
            ['model' => 'EVGA SuperNOVA 850 G5', 'wattage' => '850W', 'efficiency' => '80+ Gold', 'status' => 1],
            ['model' => 'Thermaltake Smart 600W', 'wattage' => '600W', 'efficiency' => '80+ White', 'status' => 3],
            ['model' => 'Cooler Master MWE 550', 'wattage' => '550W', 'efficiency' => '80+ Bronze', 'status' => 1],
        ];

        foreach ($psus as $psu) {
            Psu::create($psu);
        }

        $dvdRoms = [
            ['model' => 'LG GH24NSD1', 'type' => 'DVD Writer', 'status' => 1],
            ['model' => 'Samsung SH-224DB', 'type' => 'DVD Writer', 'status' => 1],
            ['model' => 'Pioneer DVR-221', 'type' => 'DVD Writer', 'status' => 1],
            ['model' => 'ASUS DRW-24B1ST', 'type' => 'DVD Writer', 'status' => 3],
            ['model' => 'Lite-On iHAS124', 'type' => 'DVD Writer', 'status' => 1],
        ];

        foreach ($dvdRoms as $dvdRom) {
            DvdRom::create($dvdRom);
        }

        // Create Computers with assigned components
        $computers = [];
        for ($i = 1; $i <= 25; $i++) {
            $status = rand(1, 3); // 1=Available, 2=Deployed, 3=Under Repair
            
            $computer = Computer::create([
                'computer_name' => 'PC-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'asset_tag' => 'UOC-' . date('Y') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'pc_number' => 'PC' . $i,
                'serial_number' => 'SN' . strtoupper(uniqid()) . rand(1000, 9999),
                'department_id' => rand(1, 6),
                'laboratory_id' => rand(1, 3),
                'processor_id' => rand(1, 5),
                'motherboard_id' => rand(1, 5),
                'ram_id' => rand(1, 5),
                'storage_id' => rand(1, 5),
                'video_card_id' => rand(1, 5),
                'psu_id' => rand(1, 5),
                'dvd_rom_id' => rand(1, 5),
                'status' => $status,
                'assigned_to' => $status == 2 ? 'Employee ' . $i : null,
                'purchase_date' => now()->subDays(rand(30, 730)),
                'warranty_expiry' => now()->addDays(rand(365, 1095)),
            ]);
            
            $computers[] = $computer;
        }

        // Create Deployments
        $deploymentCount = 0;
        foreach ($computers as $computer) {
            if ($computer->status == 2 && $deploymentCount < 15) {
                Deployment::create([
                    'computer_id' => $computer->id,
                    'department_id' => $computer->department_id,
                    'laboratory_id' => $computer->laboratory_id,
                    'deployed_by' => $admin->id,
                    'deployment_date' => now()->subDays(rand(1, 90)),
                    'expected_return_date' => now()->addDays(rand(30, 180)),
                    'status' => 'Active',
                    'notes' => 'Deployed for regular use',
                ]);
                $deploymentCount++;
            }
        }

        // Create Maintenance Records
        $issueTypes = [
            'Hardware Failure',
            'Software Issue',
            'Network Problem',
            'Power Supply Issue',
            'Overheating',
            'Memory Failure',
            'Storage Issue',
            'Display Problem'
        ];

        for ($i = 1; $i <= 20; $i++) {
            $computer = $computers[array_rand($computers)];
            $reportedDate = now()->subDays(rand(1, 180));
            $resolvedDate = $reportedDate->copy()->addDays(rand(1, 30));
            
            MaintenanceRecord::create([
                'computer_id' => $computer->id,
                'issue_type' => $issueTypes[array_rand($issueTypes)],
                'description' => "Maintenance issue reported for {$computer->computer_name}: " . $issueTypes[array_rand($issueTypes)],
                'status' => 'Completed',
                'reported_date' => $reportedDate,
                'resolved_date' => $resolvedDate,
                'cost' => rand(50, 800),
                'technician_id' => $technician->id,
                'notes' => 'Issue resolved successfully',
            ]);
        }

        // Create some pending maintenance records
        for ($i = 1; $i <= 5; $i++) {
            $computer = $computers[array_rand($computers)];
            
            MaintenanceRecord::create([
                'computer_id' => $computer->id,
                'issue_type' => $issueTypes[array_rand($issueTypes)],
                'description' => "Pending maintenance for {$computer->computer_name}",
                'status' => 'Pending',
                'reported_date' => now()->subDays(rand(1, 7)),
                'resolved_date' => null,
                'cost' => null,
                'technician_id' => null,
                'notes' => 'Waiting for technician assignment',
            ]);
        }

        $this->command->info('SimpleSeeder completed successfully!');
        $this->command->info('Created:');
        $this->command->info('- 2 Users (Admin, Technician)');
        $this->command->info('- 6 Departments');
        $this->command->info('- 3 Laboratories');
        $this->command->info('- 5 of each component type');
        $this->command->info('- 25 Computers with assigned components');
        $this->command->info('- 15 Active Deployments');
        $this->command->info('- 25 Maintenance Records (20 completed, 5 pending)');
        $this->command->info('');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@university.edu / password');
        $this->command->info('Technician: tech@university.edu / password');
    }
}
