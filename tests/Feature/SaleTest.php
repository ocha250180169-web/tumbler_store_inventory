<?php

namespace Tests\Feature;

use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesStoreData;
use Tests\TestCase;

class SaleTest extends TestCase
{
    use RefreshDatabase, CreatesStoreData;

    public function test_sale_reduces_stock_and_server_calculates_total(): void
    {
        $staff = $this->staff();
        $product = $this->product(['stock' => 10, 'selling_price' => 50000]);

        $this->actingAs($staff)->post(route('sales.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 3]],
        ])->assertSessionHasNoErrors();

        $this->assertSame(7, $product->fresh()->stock);

        $sale = Sale::firstOrFail();
        $this->assertSame(Sale::STATUS_COMPLETED, $sale->status);
        $this->assertSame($staff->id, $sale->user_id);
        $this->assertEquals(150000.00, (float) $sale->total);
        $this->assertCount(1, $sale->items);
    }

    public function test_total_and_price_from_browser_are_ignored(): void
    {
        $product = $this->product(['stock' => 10, 'selling_price' => 50000]);

        $this->actingAs($this->staff())->post(route('sales.store'), [
            'total' => 1,                     // dicoba dimanipulasi
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 2,
                'unit_price' => 1,            // dicoba dimanipulasi
                'subtotal' => 1,
            ]],
        ])->assertSessionHasNoErrors();

        $sale = Sale::firstOrFail();
        $this->assertEquals(100000.00, (float) $sale->total);
        $this->assertEquals(50000.00, (float) $sale->items->first()->unit_price);
    }

    public function test_sale_with_insufficient_stock_is_rejected_without_any_change(): void
    {
        $product = $this->product(['stock' => 4]);

        $this->actingAs($this->staff())->post(route('sales.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 6]],
        ])->assertSessionHasErrors('items');

        $this->assertSame(4, $product->fresh()->stock);
        $this->assertDatabaseCount('sales', 0);
        $this->assertDatabaseCount('sale_items', 0);
    }

    public function test_failure_on_one_item_rolls_back_the_whole_transaction(): void
    {
        // p1 dibuat lebih dulu (id lebih kecil) sehingga diproses lebih dulu dan stoknya sempat berkurang,
        // lalu p2 gagal -> semuanya harus dikembalikan.
        $p1 = $this->product(['stock' => 10]);
        $p2 = $this->product(['stock' => 1]);

        $this->actingAs($this->staff())->post(route('sales.store'), [
            'items' => [
                ['product_id' => $p1->id, 'quantity' => 2],
                ['product_id' => $p2->id, 'quantity' => 5],
            ],
        ])->assertSessionHasErrors('items');

        $this->assertSame(10, $p1->fresh()->stock);
        $this->assertSame(1, $p2->fresh()->stock);
        $this->assertSame(0, Sale::count());
        $this->assertSame(0, SaleItem::count());
    }

    public function test_same_product_on_multiple_rows_is_merged_before_stock_check(): void
    {
        $product = $this->product(['stock' => 5]);

        // 3 + 3 = 6 > 5, harus ditolak walaupun masing-masing baris (3) terlihat cukup
        $this->actingAs($this->staff())->post(route('sales.store'), [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 3],
                ['product_id' => $product->id, 'quantity' => 3],
            ],
        ])->assertSessionHasErrors('items');

        $this->assertSame(5, $product->fresh()->stock);
        $this->assertDatabaseCount('sales', 0);
    }

    public function test_invalid_quantity_is_rejected(): void
    {
        $product = $this->product(['stock' => 5]);

        foreach ([0, -3, 'abc', ''] as $bad) {
            $this->actingAs($this->staff())->post(route('sales.store'), [
                'items' => [['product_id' => $product->id, 'quantity' => $bad]],
            ])->assertSessionHasErrors('items.0.quantity');
        }

        $this->assertSame(5, $product->fresh()->stock);
        $this->assertDatabaseCount('sales', 0);
    }

    public function test_sale_requires_at_least_one_item(): void
    {
        $this->actingAs($this->staff())->post(route('sales.store'), [])
            ->assertSessionHasErrors('items');
    }

    public function test_admin_cancel_restores_stock_and_cannot_cancel_twice(): void
    {
        $admin = $this->admin();
        $product = $this->product(['stock' => 10]);

        $this->actingAs($admin)->post(route('sales.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 4]],
        ]);
        $this->assertSame(6, $product->fresh()->stock);

        $sale = Sale::firstOrFail();

        $this->actingAs($admin)->post(route('sales.cancel', $sale))->assertSessionHasNoErrors();
        $this->assertSame(10, $product->fresh()->stock);
        $this->assertSame(Sale::STATUS_CANCELLED, $sale->fresh()->status);

        // pembatalan kedua tidak boleh mengembalikan stok lagi
        $this->actingAs($admin)->post(route('sales.cancel', $sale))->assertSessionHasErrors('status');
        $this->assertSame(10, $product->fresh()->stock);
    }
}
