<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $year = now('Asia/Jakarta')->format('Y');

        $orders = [
            [
                'order_number' => sprintf('SO-%s-%05d', $year, 1),
                'order_date' => sprintf('%s-01-15', $year),
                'customer' => 'CV. Citra Mandiri',
                'lines' => [
                    $this->line('Oli Mesin Honda Genuine 0W-20 4L', 4, 425000, 5, 'Penggantian oli untuk unit operasional Honda Brio.'),
                    $this->line('Filter Oli Honda Brio', 4, 85000, 0, 'Sekalian ganti filter oli setiap servis berkala.'),
                    $this->line('Service Berkala 10.000 KM', 2, 450000, 0, 'Paket servis untuk armada harian.'),
                ],
            ],
            [
                'order_number' => sprintf('SO-%s-%05d', $year, 2),
                'order_date' => sprintf('%s-02-08', $year),
                'customer' => 'PT. Sejahtera Abadi',
                'lines' => [
                    $this->line('Kampas Rem Depan Toyota Rush', 6, 410000, 3, 'Pengadaan spare part untuk unit Toyota Rush.'),
                    $this->line('Air Filter Toyota', 6, 130000, 0, 'Penggantian untuk maintenance rutin.'),
                    $this->line('Spooring & Balancing', 2, 350000, 0, 'Pekerjaan perawatan kendaraan operasional.'),
                ],
            ],
            [
                'order_number' => sprintf('SO-%s-%05d', $year, 3),
                'order_date' => sprintf('%s-03-21', $year),
                'customer' => 'PT. Sukses Makmur',
                'lines' => [
                    $this->line('Aki GS Astra NS60L', 3, 980000, 0, 'Penggantian aki untuk kendaraan fleet.'),
                    $this->line('Wiper Blade Toyota Innova Set', 3, 185000, 0, 'Peremajaan perlengkapan hujan.'),
                    $this->line('Engine Coolant 4L', 3, 155000, 0, 'Pengisian coolant untuk servis berkala.'),
                    $this->line('Pembersihan Throttle Body', 2, 225000, 0, 'Menjaga performa mesin tetap optimal.'),
                ],
            ],
            [
                'order_number' => sprintf('SO-%s-%05d', $year, 4),
                'order_date' => sprintf('%s-04-11', $year),
                'customer' => 'UD. Jaya Sentosa',
                'lines' => [
                    $this->line('Head Unit Android 9 Inch', 1, 2450000, 0, 'Upgrade interior kendaraan demo.'),
                    $this->line('Dash Cam Full HD', 2, 725000, 0, 'Pemasangan aksesoris keselamatan.'),
                    $this->line('Floor Mat Honda Brio', 2, 320000, 0, 'Aksesoris tambahan unit display.'),
                ],
            ],
            [
                'order_number' => sprintf('SO-%s-%05d', $year, 5),
                'order_date' => sprintf('%s-05-18', $year),
                'customer' => 'PT. Berkah Mandiri',
                'lines' => [
                    $this->line('Shock Absorber Depan Honda', 4, 1250000, 2, 'Penggantian komponen suspensi.'),
                    $this->line('Power Window Motor', 4, 680000, 0, 'Perbaikan unit pintu kendaraan fleet.'),
                    $this->line('Car Care Premium', 1, 550000, 0, 'Paket detailing kendaraan kantor.'),
                ],
            ],
        ];

        foreach ($orders as $orderData) {
            $customer = Customer::query()
                ->where('full_name', $orderData['customer'])
                ->first();

            if (! $customer) {
                throw new RuntimeException(sprintf('Customer "%s" not found.', $orderData['customer']));
            }

            DB::transaction(function () use ($customer, $orderData): void {
                $summary = $this->calculateSummary($orderData['lines']);

                $order = Order::updateOrCreate(
                    ['order_number' => $orderData['order_number']],
                    [
                        'order_date' => $orderData['order_date'],
                        'customer_id' => $customer->id,
                        ...$summary,
                    ],
                );

                $order->orderItems()->delete();
                $order->orderItems()->createMany($orderData['lines']);
            });
        }
    }

    /**
     * @return array<string, string>
     */
    private function line(
        string $description,
        int $quantity,
        int $unitPrice,
        float $discountPercent = 0,
        string $note = '',
    ): array {
        $gross = $quantity * $unitPrice;
        $discountAmount = (int) round($gross * ($discountPercent / 100));
        $lineTotal = max(0, $gross - $discountAmount);

        return [
            'item_id' => $this->resolveItemId($description),
            'description' => $note,
            'quantity' => $quantity,
            'unit_price' => (string) $unitPrice,
            'discount_amount' => (string) $discountAmount,
            'line_total' => (string) $lineTotal,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function calculateSummary(array $lines): array
    {
        $subtotal = 0;
        $discountAmount = 0;

        foreach ($lines as $line) {
            $subtotal += (int) $line['unit_price'] * (int) $line['quantity'];
            $discountAmount += (int) $line['discount_amount'];
        }

        $netAmount = max(0, $subtotal - $discountAmount);
        $dppAmount = $netAmount;
        $ppnAmount = (int) round($dppAmount * 0.11);
        $grandTotal = $dppAmount + $ppnAmount;

        return [
            'subtotal' => (string) $subtotal,
            'discount_amount' => (string) $discountAmount,
            'net_amount' => (string) $netAmount,
            'dpp_amount' => (string) $dppAmount,
            'ppn_amount' => (string) $ppnAmount,
            'grand_total' => (string) $grandTotal,
        ];
    }

    private function resolveItemId(string $description): int
    {
        $item = Item::query()
            ->where('description', $description)
            ->first();

        if (! $item) {
            throw new RuntimeException(sprintf('Item "%s" not found.', $description));
        }

        return (int) $item->id;
    }
}
