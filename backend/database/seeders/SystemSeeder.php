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

class SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Users
        $admin = User::create([
            'user_id' => 'U001',
            'name' => 'System Administrator',
            'email' => 'admin@university.edu',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'address' => 'University Campus, Main Building',
            'birthdate' => '1990-01-15',
            'contact_person' => 'IT Department',
            'contact_number' => '123-456-7890',
            'department_id' => 3, // IT Department
            'is_active' => true,
        ]);

        $technician = User::create([
            'user_id' => 'U002',
            'name' => 'John Technician',
            'email' => 'tech@university.edu',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'address' => 'University Campus, IT Building',
            'birthdate' => '1992-05-20',
            'contact_person' => 'IT Department',
            'contact_number' => '123-456-7891',
            'department_id' => 3, // IT Department
            'is_active' => true,
        ]);

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
            ['name' => 'Laboratory A - Programming', 'description' => 'Programming and development lab'],
            ['name' => 'Laboratory B - Multimedia', 'description' => 'Multimedia and design lab'],
            ['name' => 'Laboratory C - Research', 'description' => 'Research and analysis lab'],
        ];

        foreach ($laboratories as $lab) {
            Laboratory::create($lab);
        }

        // Create Computer Components
        $processors = [
            ['model' => 'Intel Core i7-10700K', 'speed' => '3.8GHz', 'cores' => 8, 'status' => 'Working'],
            ['model' => 'AMD Ryzen 9 5900X', 'speed' => '3.7GHz', 'cores' => 12, 'status' => 'Working'],
            ['model' => 'Intel Core i5-11600K', 'speed' => '3.9GHz', 'cores' => 6, 'status' => 'Working'],
            ['model' => 'AMD Ryzen 7 5800X', 'speed' => '3.8GHz', 'cores' => 8, 'status' => 'Under Repair'],
            ['model' => 'Intel Core i3-10100', 'speed' => '3.6GHz', 'cores' => 4, 'status' => 'Working'],
        ];

        foreach ($processors as $processor) {
            Processor::create($processor);
        }

        $motherboards = [
            ['model' => 'ASUS ROG STRIX Z590-E', 'chipset' => 'Z590', 'status' => 'Working'],
            ['model' => 'MSI MPG X570 GAMING PLUS', 'chipset' => 'X570', 'status' => 'Working'],
            ['model' => 'GIGABYTE B550 AORUS PRO', 'chipset' => 'B550', 'status' => 'Working'],
            ['model' => 'ASRock B450M Steel Legend', 'chipset' => 'B450', 'status' => 'Defective'],
            ['model' => 'ASUS PRIME H510M', 'chipset' => 'H510', 'status' => 'Working'],
        ];

        foreach ($motherboards as $motherboard) {
            Motherboard::create($motherboard);
        }

        $rams = [
            ['capacity' => '16GB', 'type' => 'DDR4', 'speed' => '3200MHz', 'status' => 'Working'],
            ['capacity' => '32GB', 'type' => 'DDR4', 'speed' => '3200MHz', 'status' => 'Working'],
            ['capacity' => '8GB', 'type' => 'DDR4', 'speed' => '2666MHz', 'status' => 'Working'],
            ['capacity' => '64GB', 'type' => 'DDR4', 'speed' => '3600MHz', 'status' => 'Working'],
            ['capacity' => '16GB', 'type' => 'DDR4', 'speed' => '2400MHz', 'status' => 'Under Repair'],
        ];

        foreach ($rams as $ram) {
            Ram::create($ram);
        }

        $storages = [
            ['capacity' => '512GB', 'type' => 'NVMe SSD', 'brand' => 'Samsung', 'status' => 'Working'],
            ['capacity' => '1TB', 'type' => 'NVMe SSD', 'brand' => 'Western Digital', 'status' => 'Working'],
            ['capacity' => '2TB', 'type' => 'SATA SSD', 'brand' => 'Crucial', 'status' => 'Working'],
            ['capacity' => '4TB', 'type' => 'HDD', 'brand' => 'Seagate', 'status' => 'Working'],
            ['capacity' => '256GB', 'type' => 'SATA SSD', 'brand' => 'Kingston', 'status' => 'Defective'],
        ];

        foreach ($storages as $storage) {
            Storage::create($storage);
        }

        $videoCards = [
            ['model' => 'NVIDIA GeForce RTX 3070', 'memory' => '8GB GDDR6', 'status' => 'Working'],
            ['model' => 'AMD Radeon RX 6700 XT', 'memory' => '12GB GDDR6', 'status' => 'Working'],
            ['model' => 'NVIDIA GeForce GTX 1660', 'memory' => '6GB GDDR5', 'status' => 'Working'],
            ['model' => 'AMD Radeon RX 580', 'memory' => '8GB GDDR5', 'status' => 'Under Repair'],
            ['model' => 'Intel UHD Graphics 730', 'memory' => '4GB', 'status' => 'Working'],
        ];

        foreach ($videoCards as $videoCard) {
            VideoCard::create($videoCard);
        }

        $psus = [
            ['model' => 'Corsair RM750X', 'wattage' => '750W', 'efficiency' => '80+ Gold', 'status' => 'Working'],
            ['model' => 'Seasonic Focus GX-650', 'wattage' => '650W', 'efficiency' => '80+ Gold', 'status' => 'Working'],
            ['model' => 'EVGA SuperNOVA 850 G5', 'wattage' => '850W', 'efficiency' => '80+ Gold', 'status' => 'Working'],
            ['model' => 'Thermaltake Smart 600W', 'wattage' => '600W', 'efficiency' => '80+ White', 'status' => 'Defective'],
            ['model' => 'Cooler Master MWE 550', 'wattage' => '550W', 'efficiency' => '80+ Bronze', 'status' => 'Working'],
        ];

        foreach ($psus as $psu) {
            Psu::create($psu);
        }

        $dvdRoms = [
            ['model' => 'LG GH24NSD1', 'type' => 'DVD Writer', 'status' => 'Working'],
            ['model' => 'Samsung SH-224DB', 'type' => 'DVD Writer', 'status' => 'Working'],
            ['model' => 'Pioneer DVR-221', 'type' => 'DVD Writer', 'status' => 'Working'],
            ['model' => 'ASUS DRW-24B1ST', 'type' => 'DVD Writer', 'status' => 'Defective'],
            ['model' => 'Lite-On iHAS124', 'type' => 'DVD Writer', 'status' => 'Working'],
        ];

        foreach ($dvdRoms as $dvdRom) {
            DvdRom::create($dvdRom);
        }

        // Create Computers with assigned components
        $computers = [];
        for ($i = 1; $i <= 15; $i++) {
            $status = ['Available', 'Deployed', 'Under Repair'][array_rand(['Available', 'Deployed', 'Under Repair'])];
            
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
                'assigned_to' => $status === 'Deployed' ? 'Employee ' . $i : null,
                'purchase_date' => now()->subDays(rand(30, 730)),
                'warranty_expiry' => now()->addDays(rand(365, 1095)),
            ]);
            
            $computers[] = $computer;
        }

        // Create Deployments
        $deploymentCount = 0;
        foreach ($computers as $computer) {
            if ($computer->status === 'Deployed' && $deploymentCount < 8) {
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

        for ($i = 1; $i <= 12; $i++) {
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
        for ($i = 1; $i <= 3; $i++) {
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

        $this->command->info('SystemSeeder completed successfully!');
        $this->command->info('Created:');
        $this->command->info('- 2 Users (Admin, Technician)');
        $this->command->info('- 6 Departments');
        $this->command->info('- 3 Laboratories');
        $this->command->info('- 5 of each component type');
        $this->command->info('- 15 Computers with assigned components');
        $this->command->info('- 8 Active Deployments');
        $this->command->info('- 15 Maintenance Records (12 completed, 3 pending)');
    }
}
