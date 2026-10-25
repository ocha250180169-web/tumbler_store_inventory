<?php

namespace Tests\Feature;

use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesStoreData;
use Tests\TestCase;

class SearchAndDashboardTest extends TestCase
{
    use RefreshDatabase, CreatesStoreData;

    public function test_product_search_by_name_and_sku(): void
    {
        $this->product(['name' => 'Tumbler Kaca Unik', 'sku' => 'AAA-111']);
        $this->product(['name' => 'Botol Plastik Biasa', 'sku' => 'BBB-222']);

        $this->actingAs($this->staff());

        $this->get(route('products.index', ['q' => 'Kaca']))
            ->assertSee('Tumbler Kaca Unik')
            ->assertDontSee('Botol Plastik Biasa');

        $this->get(route('products.index', ['q' => 'BBB-222']))
            ->assertSee('Botol Plastik Biasa')
            ->assertDontSee('Tumbler Kaca Unik');
    }

    public function test_product_filter_by_category(): void
    {
        $steel = $this->category('Stainless');
        $glass = $this->category('Kaca');
        $this->product(['category_id' => $steel->id, 'name' => 'Produk Baja']);
        $this->product(['category_id' => $glass->id, 'name' => 'Produk Gelas']);

        $this->actingAs($this->staff())
            ->get(route('products.index', ['category_id' => $steel->id]))
            ->assertSee('Produk Baja')
            ->assertDontSee('Produk Gelas');
    }

    public function test_pagination_limits_rows_and_keeps_filter_in_links(): void
    {
        $category = $this->category();
        foreach (range(1, 15) as $i) {
            $this->product(['category_id' => $category->id, 'name' => sprintf('Item %02d', $i)]);
        }

        $this->actingAs($this->staff());

        $this->get(route('products.index', ['category_id' => $category->id]))
            ->assertSee('Item 01')
            ->assertDontSee('Item 15')              // baris ke-11 s/d 15 ada di halaman 2
            ->assertSee('category_id=' . $category->id);   // filter terbawa di link pagination

        $this->get(route('products.index', ['category_id' => $category->id, 'page' => 2]))
            ->assertSee('Item 15')
            ->assertDontSee('Item 01');
    }

    public function test_sales_filter_by_status_and_date(): void
    {
        $admin = $this->admin();
        $product = $this->product(['stock' => 50]);

        $this->actingAs($admin)->post(route('sales.store'), ['items' => [['product_id' => $product->id, 'quantity' => 1]]]);
        $this->actingAs($admin)->post(route('sales.store'), ['items' => [['product_id' => $product->id, 'quantity' => 2]]]);

        [$first, $second] = Sale::orderBy('id')->get()->all();
        $this->actingAs($admin)->post(route('sales.cancel', $second));

        $this->actingAs($admin)->get(route('sales.index', ['status' => 'cancelled']))
            ->assertSee($second->sale_number)
            ->assertDontSee($first->sale_number);

        $this->actingAs($admin)->get(route('sales.index', ['date_from' => now()->addDay()->toDateString()]))
            ->assertDontSee($first->sale_number);

        $this->actingAs($admin)->get(route('sales.index', [
            'date_from' => now()->toDateString(), 'date_to' => now()->toDateString(),
        ]))->assertSee($first->sale_number);
    }

    public function test_invalid_date_range_is_rejected(): void
    {
        $this->actingAs($this->admin())
            ->get(route('sales.index', ['date_from' => '2026-10-10', 'date_to' => '2026-10-01']))
            ->assertSessionHasErrors('date_to');
    }

    public function test_low_stock_page_lists_only_low_stock_products(): void
    {
        $this->product(['name' => 'Stok Aman', 'stock' => 50, 'minimum_stock' => 5]);
        $this->product(['name' => 'Stok Tipis', 'stock' => 3, 'minimum_stock' => 5]);

        $this->actingAs($this->staff())->get(route('products.low-stock'))
            ->assertSee('Stok Tipis')
            ->assertDontSee('Stok Aman');
    }

    public function test_dashboard_numbers_come_from_database(): void
    {
        $admin = $this->admin();
        $product = $this->product(['stock' => 20, 'selling_price' => 50000]);
        $this->product(['stock' => 1, 'minimum_stock' => 5]); // 1 produk stok rendah

        $this->actingAs($admin)->post(route('sales.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 3]],
        ]);

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Rp 150.000')   // penjualan hari ini = 3 x 50.000
            ->assertSee('Produk Stok Rendah');
    }
}
