<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesStoreData;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase, CreatesStoreData;

    private function validPayload(array $override = []): array
    {
        return array_merge([
            'sku' => 'NEW-001',
            'name' => 'Tumbler Baru',
            'category_id' => $this->category()->id,
            'purchase_price' => 40000,
            'selling_price' => 80000,
            'minimum_stock' => 5,
        ], $override);
    }

    public function test_admin_can_create_product_with_zero_initial_stock(): void
    {
        $this->actingAs($this->admin())
            ->post(route('products.store'), $this->validPayload())
            ->assertSessionHasNoErrors();

        $product = Product::where('sku', 'NEW-001')->firstOrFail();
        $this->assertSame(0, $product->stock);
    }

    public function test_duplicate_sku_is_rejected(): void
    {
        $existing = $this->product(['sku' => 'DUP-001']);

        $this->actingAs($this->admin())
            ->post(route('products.store'), $this->validPayload(['sku' => 'DUP-001']))
            ->assertSessionHasErrors('sku');

        $this->assertSame(1, Product::where('sku', 'DUP-001')->count());
    }

    public function test_negative_price_is_rejected(): void
    {
        $this->actingAs($this->admin())
            ->post(route('products.store'), $this->validPayload(['selling_price' => -1, 'purchase_price' => -5]))
            ->assertSessionHasErrors(['selling_price', 'purchase_price']);

        $this->assertDatabaseCount('products', 0);
    }

    public function test_invalid_category_is_rejected(): void
    {
        $this->actingAs($this->admin())
            ->post(route('products.store'), $this->validPayload(['category_id' => 9999]))
            ->assertSessionHasErrors('category_id');
    }

    public function test_updating_product_cannot_change_stock(): void
    {
        $product = $this->product(['stock' => 10]);

        $this->actingAs($this->admin())
            ->put(route('products.update', $product), $this->validPayload([
                'sku' => $product->sku,
                'category_id' => $product->category_id,
                'stock' => 9999, // field liar dari browser harus diabaikan
            ]))
            ->assertSessionHasNoErrors();

        $this->assertSame(10, $product->fresh()->stock);
    }

    public function test_product_used_in_transaction_cannot_be_deleted(): void
    {
        $admin = $this->admin();
        $product = $this->product(['stock' => 10]);

        $this->actingAs($admin)->post(route('sales.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ]);

        $this->actingAs($admin)->delete(route('products.destroy', $product))
            ->assertSessionHas('error');

        $this->assertNotSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_unused_product_is_soft_deleted(): void
    {
        $product = $this->product();

        $this->actingAs($this->admin())->delete(route('products.destroy', $product))
            ->assertSessionHas('success');

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $product = $this->product();

        $this->actingAs($this->admin())
            ->delete(route('categories.destroy', $product->category_id))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $product->category_id]);
    }
}
