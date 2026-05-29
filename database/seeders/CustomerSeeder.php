<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            ['full_name' => 'PT. Sejahtera Abadi', 'address' => 'Jl. Merdeka No. 10, Jakarta Pusat'],
            ['full_name' => 'CV. Maju Bersama', 'address' => 'Jl. Sudirman No. 25, Bandung'],
            ['full_name' => 'PT. Sukses Makmur', 'address' => 'Jl. Ahmad Yani No. 88, Surabaya'],
            ['full_name' => 'UD. Jaya Sentosa', 'address' => 'Jl. Diponegoro No. 15, Semarang'],
            ['full_name' => 'PT. Berkah Mandiri', 'address' => 'Jl. Gatot Subroto No. 70, Medan'],
            ['full_name' => 'CV. Prima Jaya', 'address' => 'Jl. Asia Afrika No. 12, Bandung'],
            ['full_name' => 'PT. Cipta Niaga', 'address' => 'Jl. Hayam Wuruk No. 33, Jakarta Barat'],
            ['full_name' => 'UD. Sumber Rejeki', 'address' => 'Jl. Raya Solo No. 45, Yogyakarta'],
            ['full_name' => 'PT. Mega Karya', 'address' => 'Jl. Pahlawan No. 21, Makassar'],
            ['full_name' => 'CV. Langgeng Abadi', 'address' => 'Jl. Imam Bonjol No. 18, Pekanbaru'],
            ['full_name' => 'PT. Bintang Timur', 'address' => 'Jl. Diponegoro No. 56, Denpasar'],
            ['full_name' => 'UD. Sinar Jaya', 'address' => 'Jl. Veteran No. 77, Malang'],
            ['full_name' => 'PT. Nusantara Jaya', 'address' => 'Jl. Letjen S. Parman No. 9, Jakarta Barat'],
            ['full_name' => 'CV. Makmur Sejahtera', 'address' => 'Jl. Pemuda No. 8, Semarang'],
            ['full_name' => 'PT. Artha Mandiri', 'address' => 'Jl. Gajah Mada No. 41, Surabaya'],
            ['full_name' => 'UD. Karya Abadi', 'address' => 'Jl. Cut Meutia No. 19, Medan'],
            ['full_name' => 'PT. Mitra Utama', 'address' => 'Jl. Sultan Agung No. 62, Bandung'],
            ['full_name' => 'CV. Sukses Jaya', 'address' => 'Jl. Teuku Umar No. 14, Denpasar'],
            ['full_name' => 'PT. Gemilang Persada', 'address' => 'Jl. Hos Cokroaminoto No. 29, Jakarta Pusat'],
            ['full_name' => 'UD. Tunas Harapan', 'address' => 'Jl. Pasar Baru No. 11, Surabaya'],
            ['full_name' => 'PT. Sentosa Alam', 'address' => 'Jl. Soekarno Hatta No. 90, Bandung'],
            ['full_name' => 'CV. Citra Mandiri', 'address' => 'Jl. Kartini No. 24, Semarang'],
            ['full_name' => 'PT. Karya Prima', 'address' => 'Jl. Dipati Ukur No. 6, Bandung'],
            ['full_name' => 'UD. Makmur Sentosa', 'address' => 'Jl. A. Yani No. 51, Banjarmasin'],
            ['full_name' => 'PT. Global Niaga', 'address' => 'Jl. Veteran No. 73, Yogyakarta'],
            ['full_name' => 'CV. Mitra Usaha', 'address' => 'Jl. Riau No. 37, Pekanbaru'],
            ['full_name' => 'PT. Cahaya Abadi', 'address' => 'Jl. Panglima Sudirman No. 82, Surabaya'],
            ['full_name' => 'UD. Sumber Makmur', 'address' => 'Jl. Kertajaya No. 16, Surabaya'],
            ['full_name' => 'PT. Harmoni Jaya', 'address' => 'Jl. Slamet Riyadi No. 44, Solo'],
            ['full_name' => 'CV. Prima Sentosa', 'address' => 'Jl. K.H. Wahid Hasyim No. 28, Jakarta Pusat'],
        ];

        foreach ($customers as $index => $customer) {
            $customerCode = sprintf('CUST-%04d', $index + 1);

            Customer::updateOrCreate(
                ['customer_code' => $customerCode],
                [
                    ...$customer,
                    'customer_code' => $customerCode,
                    'phone_number' => sprintf('0812%06d', $index + 1),
                ],
            );
        }
    }
}
