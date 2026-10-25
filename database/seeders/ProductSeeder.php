<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // [kategori, sku, nama, harga beli, harga jual, stok awal, stok minimum]
        $rows = [
            ['Stainless Steel', 'TMB-SS-350', 'Tumbler Stainless Classic 350 ml', 45000, 89000, 40, 10],
            ['Stainless Steel', 'TMB-SS-500', 'Tumbler Stainless Classic 500 ml', 55000, 109000, 35, 10],
            ['Stainless Steel', 'TMB-SS-750', 'Tumbler Stainless Vacuum 750 ml', 75000, 149000, 8, 10],   // stok rendah
            ['Stainless Steel', 'TMB-SS-900', 'Tumbler Stainless Vacuum 900 ml Handle', 95000, 189000, 25, 8],
            ['Stainless Steel', 'TMB-SS-1200', 'Tumbler Jumbo 1200 ml', 120000, 239000, 4, 5],            // stok rendah
            ['Plastik BPA Free', 'TMB-PL-600', 'Tumbler Plastik Tritan 600 ml', 20000, 45000, 60, 15],
            ['Plastik BPA Free', 'TMB-PL-800', 'Tumbler Plastik Sport 800 ml', 25000, 55000, 3, 12],     // stok rendah
            ['Plastik BPA Free', 'TMB-PL-1000', 'Tumbler Plastik Motivasi 1 L', 30000, 65000, 48, 15],
            ['Kaca Borosilikat', 'TMB-GL-400', 'Tumbler Kaca Borosilikat 400 ml', 40000, 85000, 30, 8],
            ['Kaca Borosilikat', 'TMB-GL-550', 'Tumbler Kaca Infuser 550 ml', 50000, 99000, 20, 8],
            ['Tumbler Anak', 'TMB-KD-300', 'Tumbler Anak Sedotan Silikon 300 ml', 30000, 65000, 35, 10],
            ['Tumbler Anak', 'TMB-KD-450', 'Tumbler Anak Motif Hewan 450 ml', 38000, 79000, 12, 12],     // stok rendah
            ['Aksesori', 'ACC-STR-01', 'Sedotan Stainless Set 4 pcs', 12000, 29000, 80, 20],
            ['Aksesori', 'ACC-BRS-01', 'Sikat Pembersih Tumbler', 8000, 19000, 70, 20],
            ['Aksesori', 'ACC-BAG-01', 'Tas Pembawa Tumbler Neoprene', 20000, 45000, 18, 10],
        ];

        foreach ($rows as [$category, $sku, $name, $cost, $price, $stock, $min]) {
            if (Product::where('sku', $sku)->exists()) {
                continue;
            }

            // 'stock' tidak fillable, jadi seeder memakai unguarded() untuk stok awal.
            Product::unguarded(fn () => Product::create([
                'category_id' => Category::where('name', $category)->value('id'),
                'sku' => $sku,
                'name' => $name,
                'purchase_price' => $cost,
                'selling_price' => $price,
                'stock' => $stock,
                'minimum_stock' => $min,
            ]));
        }
    }
}
