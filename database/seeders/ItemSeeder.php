<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['description' => 'Oli Mesin Honda Genuine 0W-20 4L', 'price' => 425000],
            ['description' => 'Oli Mesin Toyota Genuine 5W-30 4L', 'price' => 395000],
            ['description' => 'Filter Oli Honda Brio', 'price' => 85000],
            ['description' => 'Filter Oli Toyota Avanza', 'price' => 90000],
            ['description' => 'Busi Iridium Honda', 'price' => 145000],
            ['description' => 'Busi Iridium Toyota', 'price' => 145000],
            ['description' => 'Kampas Rem Depan Honda HR-V', 'price' => 385000],
            ['description' => 'Kampas Rem Depan Toyota Rush', 'price' => 410000],
            ['description' => 'Aki GS Astra 55D23L', 'price' => 1350000],
            ['description' => 'Aki GS Astra NS60L', 'price' => 980000],
            ['description' => 'Wiper Blade Honda Brio Set', 'price' => 165000],
            ['description' => 'Wiper Blade Toyota Innova Set', 'price' => 185000],
            ['description' => 'Air Filter Honda', 'price' => 125000],
            ['description' => 'Air Filter Toyota', 'price' => 130000],
            ['description' => 'AC Filter Cabin Honda', 'price' => 145000],
            ['description' => 'AC Filter Cabin Toyota', 'price' => 150000],
            ['description' => 'V-Belt Honda', 'price' => 275000],
            ['description' => 'V-Belt Toyota', 'price' => 290000],
            ['description' => 'Engine Coolant 4L', 'price' => 155000],
            ['description' => 'Brake Fluid DOT 3', 'price' => 85000],
            ['description' => 'Service Berkala 10.000 KM', 'price' => 450000],
            ['description' => 'Service Tune Up', 'price' => 650000],
            ['description' => 'Spooring & Balancing', 'price' => 350000],
            ['description' => 'Ganti Kampas Rem', 'price' => 275000],
            ['description' => 'Pembersihan Throttle Body', 'price' => 225000],
            ['description' => 'Poles Headlamp', 'price' => 180000],
            ['description' => 'Car Care Premium', 'price' => 550000],
            ['description' => 'Floor Mat Honda Brio', 'price' => 320000],
            ['description' => 'Floor Mat Toyota Avanza', 'price' => 345000],
            ['description' => 'Head Unit Android 9 Inch', 'price' => 2450000],
            ['description' => 'Dash Cam Full HD', 'price' => 725000],
            ['description' => 'Power Window Motor', 'price' => 680000],
            ['description' => 'Shock Absorber Depan Honda', 'price' => 1250000],
            ['description' => 'Shock Absorber Depan Toyota', 'price' => 1325000],
            ['description' => 'Tire Sealant Kit', 'price' => 135000],
            ['description' => 'Washer Fluid 1L', 'price' => 45000],
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
