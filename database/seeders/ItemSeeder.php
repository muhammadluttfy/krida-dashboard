<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['description' => 'Laptop ASUS Vivobook 14', 'price' => 7500000],
            ['description' => 'Printer Epson L3210', 'price' => 2350000],
            ['description' => 'Mouse Logitech M185', 'price' => 145000],
            ['description' => 'Keyboard Logitech K120', 'price' => 125000],
            ['description' => 'Monitor LG 24MK430H', 'price' => 1650000],
            ['description' => 'SSD Kingston 480GB', 'price' => 620000],
            ['description' => 'RAM DDR4 8GB', 'price' => 395000],
            ['description' => 'Flashdisk SanDisk 64GB', 'price' => 89000],
            ['description' => 'Router TP-Link Archer C6', 'price' => 525000],
            ['description' => 'Harddisk External 1TB', 'price' => 845000],
            ['description' => 'Webcam Logitech C270', 'price' => 275000],
            ['description' => 'UPS APC 650VA', 'price' => 910000],
            ['description' => 'Headset Jabra Evolve 20', 'price' => 580000],
            ['description' => 'Speaker Logitech Z213', 'price' => 390000],
            ['description' => 'Dokument Scanner Fujitsu', 'price' => 5400000],
            ['description' => 'Projector Epson XGA', 'price' => 6750000],
            ['description' => 'Cable HDMI 3m', 'price' => 65000],
            ['description' => 'Cable LAN Cat6 10m', 'price' => 85000],
            ['description' => 'Wireless Keyboard Set', 'price' => 325000],
            ['description' => 'Label Printer TSC', 'price' => 2150000],
            ['description' => 'Barcodescanner Honeywell', 'price' => 1850000],
            ['description' => 'Access Point Ubiquiti', 'price' => 1325000],
            ['description' => 'Tinta Printer Original', 'price' => 240000],
            ['description' => 'CPU Cooler RGB', 'price' => 310000],
            ['description' => 'Laptop Stand Aluminum', 'price' => 175000],
            ['description' => 'External SSD 1TB', 'price' => 1195000],
            ['description' => 'Monitor Arm Single', 'price' => 285000],
            ['description' => 'Power Strip 6 Socket', 'price' => 95000],
            ['description' => 'Microphone USB Podcast', 'price' => 460000],
            ['description' => 'Tablet Stylus Pen', 'price' => 180000],
            ['description' => 'Switch 8 Port Gigabit', 'price' => 410000],
            ['description' => 'CPU Processor Intel i5', 'price' => 2450000],
            ['description' => 'Graphics Card GTX 1650', 'price' => 3450000],
            ['description' => 'Motherboard H610M', 'price' => 1625000],
            ['description' => 'Power Supply 550W', 'price' => 590000],
            ['description' => 'Monitor LG 27 inch', 'price' => 2450000],
            ['description' => 'Printer Canon G3010', 'price' => 2650000],
            ['description' => 'Laptop Lenovo IdeaPad 3', 'price' => 6900000],
            ['description' => 'Mouse Pad Large', 'price' => 55000],
            ['description' => 'Cable USB-C 1m', 'price' => 75000],
        ];

        foreach ($items as $index => $item) {
            $itemCode = sprintf('ITEM-%04d', $index + 1);

            Item::updateOrCreate(
                ['item_code' => $itemCode],
                [
                    'item_code' => $itemCode,
                    'description' => $item['description'],
                    'price' => $item['price'],
                ],
            );
        }
    }
}
