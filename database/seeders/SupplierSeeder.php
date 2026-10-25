<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        // Data dummy (bukan data pribadi).
        $suppliers = [
            ['name' => 'CV Sinar Logam Nusantara', 'phone' => '0811-1111-0001', 'email' => 'sinarlogam@example.com'],
            ['name' => 'PT Tritan Plastik Mandiri', 'phone' => '0811-1111-0002', 'email' => 'tritan@example.com'],
            ['name' => 'UD Kaca Bening Jaya', 'phone' => '0811-1111-0003', 'email' => 'kacabening@example.com'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(['name' => $supplier['name']], $supplier);
        }
    }
}
