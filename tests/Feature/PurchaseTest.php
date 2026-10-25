<?php

namespace Tests\Feature;

use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesStoreData;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase, CreatesStoreData;

    public function test_purchase_increases_stock_by_quantity(): void
    {
        $staff = $this->staff();
        $supplier = $this->supplier();
        $p1 = $this->product(['stock' => 5]);
        $p2 = $this->product(['stock' => 0]);

        $this->actingAs($staff)->post(route('purchases.store'), [
            'supplier_id' => $supplier->id,
            'items' => [
                ['product_id' => $p1->id, 'quantity' => 10, 'unit_cost' => 20000],
                ['product_id' => $p2->id, 'quantity' => 4, 'unit_cost' => 30000],
            ],
        ])->assertSessionHasNoErrors();

        $this->assertSame(15, $p1->fresh()->stock);
        $this->assertSame(4, $p2->fresh()->stock);

        $purchase = Purchase::firstOrFail();
        $this->assertSame(Purchase::STATUS_CONFIRMED, $purchase->status);
        $this->assertSame($staff->id, $purchase->user_id);
        $this->assertEquals(320000.00, (float) $purchase->total); // 10*20000 + 4*30000
        $this->assertCount(2, $purchase->items);
    }

    public function test_purchase_with_invalid_quantity_or_cost_is_rejected(): void
    {
        $supplier = $this->supplier();
        $product = $this->product(['stock' => 5]);

        $this->actingAs($this->staff())->post(route('purchases.store'), [
            'supplier_id' => $supplier->id,
            'items' => [['product_id' => $product->id, 'quantity' => 0, 'unit_cost' => -1]],
        ])->assertSessionHasErrors(['items.0.quantity', 'items.0.unit_cost']);

        $this->assertSame(5, $product->fresh()->stock);
        $this->assertDatabaseCount('purchases', 0);
    }

    public function test_purchase_requires_supplier_and_items(): void
    {
        $this->actingAs($this->staff())->post(route('purchases.store'), [])
            ->assertSessionHasErrors(['supplier_id', 'items']);
    }

    public function test_cancelling_purchase_restores_stock_when_possible(): void
    {
        $admin = $this->admin();
        $product = $this->product(['stock' => 5]);

        $this->actingAs($admin)->post(route('purchases.store'), [
            'supplier_id' => $this->supplier()->id,
            'items' => [['product_id' => $product->id, 'quantity' => 10, 'unit_cost' => 1000]],
        ]);
        $this->assertSame(15, $product->fresh()->stock);

        $purchase = Purchase::firstOrFail();
        $this->actingAs($admin)->post(route('purchases.cancel', $purchase))
            ->assertSessionHasNoErrors();

        $this->assertSame(5, $product->fresh()->stock);
        $this->assertSame(Purchase::STATUS_CANCELLED, $purchase->fresh()->status);
    }

    public function test_cancelling_purchase_is_rejected_if_stock_already_sold(): void
    {
        $admin = $this->admin();
        $product = $this->product(['stock' => 0]);

        $this->actingAs($admin)->post(route('purchases.store'), [
            'supplier_id' => $this->supplier()->id,
            'items' => [['product_id' => $product->id, 'quantity' => 10, 'unit_cost' => 1000]],
        ]);
        // 8 unit terjual, sisa stok 2 (< 10 yang dibeli)
        $this->actingAs($admin)->post(route('sales.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 8]],
        ]);

        $purchase = Purchase::firstOrFail();
        $this->actingAs($admin)->post(route('purchases.cancel', $purchase))
            ->assertSessionHasErrors('status');

        $this->assertSame(2, $product->fresh()->stock);
        $this->assertSame(Purchase::STATUS_CONFIRMED, $purchase->fresh()->status);
    }
}
