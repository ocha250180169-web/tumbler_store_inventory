<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Stainless Steel', 'Plastik BPA Free', 'Kaca Borosilikat', 'Tumbler Anak', 'Aksesori'] as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
