<?php

namespace Tests\Concerns;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Helper pembuat data uji.
 */
trait CreatesStoreData
{
    protected function admin(): User
    {
        return User::create([
            'name' => 'Admin Test',
            'email' => 'admin-' . Str::random(5) . '@test.test',
            'password' => 'password123',
            'role' => User::ROLE_ADMIN,
        ]);
    }

    protected function staff(): User
    {
        return User::create([
            'name' => 'Staff Test',
            'email' => 'staff-' . Str::random(5) . '@test.test',
            'password' => 'password123',
            'role' => User::ROLE_STAFF,
        ]);
    }

    protected function category(?string $name = null): Category
    {
        return Category::create(['name' => $name ?? 'Kategori ' . Str::random(5)]);
    }

    protected function supplier(): Supplier
    {
        return Supplier::create(['name' => 'Pemasok ' . Str::random(5)]);
    }

    /** Stok bukan fillable, jadi diisi lewat unguarded(). */
    protected function product(array $attrs = []): Product
    {
        return Product::unguarded(fn () => Product::create(array_merge([
            'category_id' => $attrs['category_id'] ?? $this->category()->id,
            'sku' => 'SKU-' . Str::upper(Str::random(8)),
            'name' => 'Produk ' . Str::random(5),
            'purchase_price' => 50000,
            'selling_price' => 100000,
            'stock' => 10,
            'minimum_stock' => 2,
        ], $attrs)));
    }
}
