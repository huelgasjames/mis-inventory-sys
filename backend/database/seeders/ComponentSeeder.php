<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Processor;
use App\Models\Motherboard;
use App\Models\Ram;
use App\Models\Storage;
use App\Models\VideoCard;
use App\Models\Psu;
use App\Models\DvdRom;

class ComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create processors
        $processors = [
            ['model' => 'Intel Core i3-10100', 'status' => 'Available'],
            ['model' => 'Intel Core i5-10400', 'status' => 'Available'],
            ['model' => 'Intel Core i7-10700', 'status' => 'Available'],
            ['model' => 'AMD Ryzen 3 3100', 'status' => 'Available'],
            ['model' => 'AMD Ryzen 5 3600', 'status' => 'Available'],
            ['model' => 'AMD Ryzen 7 3700X', 'status' => 'In Use'],
        ];

        foreach ($processors as $processor) {
            Processor::create($processor);
        }

        // Create motherboards
        $motherboards = [
            ['model' => 'ASUS Prime H410M', 'status' => 'Available'],
            ['model' => 'Gigabyte H410M', 'status' => 'Available'],
            ['model' => 'MSI B460M', 'status' => 'Available'],
            ['model' => 'ASUS TUF B460M', 'status' => 'In Use'],
            ['model' => 'Gigabyte B550M', 'status' => 'Available'],
        ];

        foreach ($motherboards as $motherboard) {
            Motherboard::create($motherboard);
        }

        // Create RAM modules
        $rams = [
            ['capacity' => '4GB DDR4', 'status' => 'Available'],
            ['capacity' => '8GB DDR4', 'status' => 'Available'],
            ['capacity' => '16GB DDR4', 'status' => 'Available'],
            ['capacity' => '32GB DDR4', 'status' => 'In Use'],
            ['capacity' => '8GB DDR3', 'status' => 'Defective'],
        ];

        foreach ($rams as $ram) {
            Ram::create($ram);
        }

        // Create storage devices
        $storages = [
            ['capacity' => '256GB SSD', 'status' => 'Available'],
            ['capacity' => '512GB SSD', 'status' => 'Available'],
            ['capacity' => '1TB SSD', 'status' => 'In Use'],
            ['capacity' => '2TB HDD', 'status' => 'Available'],
            ['capacity' => '1TB HDD', 'status' => 'Defective'],
        ];

        foreach ($storages as $storage) {
            Storage::create($storage);
        }

        // Create video cards
        $videoCards = [
            ['model' => 'NVIDIA GTX 1650', 'status' => 'Available'],
            ['model' => 'NVIDIA GTX 1660', 'status' => 'Available'],
            ['model' => 'NVIDIA RTX 2060', 'status' => 'In Use'],
            ['model' => 'AMD RX 580', 'status' => 'Available'],
            ['model' => 'Integrated Graphics', 'status' => 'Available'],
        ];

        foreach ($videoCards as $videoCard) {
            VideoCard::create($videoCard);
        }

        // Create PSUs
        $psus = [
            ['wattage' => 350, 'status' => 'Available'],
            ['wattage' => 450, 'status' => 'Available'],
            ['wattage' => 550, 'status' => 'Available'],
            ['wattage' => 650, 'status' => 'In Use'],
            ['wattage' => 750, 'status' => 'Available'],
        ];

        foreach ($psus as $psu) {
            Psu::create($psu);
        }

        // Create DVD ROMs
        $dvdRoms = [
            ['type_field' => 'DVD-RW', 'status' => 'Available'],
            ['type_field' => 'DVD-RW', 'status' => 'Available'],
            ['type_field' => 'DVD-ROM', 'status' => 'Defective'],
            ['type_field' => 'DVD-RW', 'status' => 'In Use'],
        ];

        foreach ($dvdRoms as $dvdRom) {
            DvdRom::create($dvdRom);
        }
    }
}
