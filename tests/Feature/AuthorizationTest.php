<?php

namespace Tests\Feature;

use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesStoreData;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase, CreatesStoreData;

    public function test_staff_cannot_access_admin_pages_by_direct_url(): void
    {
        $staff = $this->staff();
        $product = $this->product();

        $this->actingAs($staff);

        $this->get(route('products.create'))->assertForbidden();
        $this->get(route('products.edit', $product))->assertForbidden();
        $this->get(route('categories.index'))->assertForbidden();
        $this->get(route('suppliers.index'))->assertForbidden();
        $this->get(route('users.index'))->assertForbidden();
    }

    public function test_staff_cannot_modify_master_data_via_post_put_delete(): void
    {
        $staff = $this->staff();
        $product = $this->product();

        $this->actingAs($staff);

        $this->post(route('products.store'), [
            'sku' => 'HACK-1', 'name' => 'x', 'category_id' => $product->category_id,
            'purchase_price' => 1, 'selling_price' => 1, 'minimum_stock' => 1,
        ])->assertForbidden();

        $this->delete(route('products.destroy', $product))->assertForbidden();
        $this->post(route('categories.store'), ['name' => 'Hack'])->assertForbidden();
        $this->post(route('users.store'), [
            'name' => 'Hack', 'email' => 'hack@test.test', 'role' => 'admin',
            'password' => 'password123', 'password_confirmation' => 'password123',
        ])->assertForbidden();

        $this->assertDatabaseHas('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('users', ['email' => 'hack@test.test']);
    }

    public function test_staff_cannot_cancel_transactions(): void
    {
        $staff = $this->staff();
        $product = $this->product();

        $this->actingAs($staff)->post(route('sales.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ]);
        $sale = Sale::firstOrFail();

        $this->actingAs($staff)->post(route('sales.cancel', $sale))->assertForbidden();
        $this->assertSame(Sale::STATUS_COMPLETED, $sale->fresh()->status);
    }

    public function test_staff_can_view_products_and_create_transactions(): void
    {
        $staff = $this->staff();

        $this->actingAs($staff);
        $this->get(route('dashboard'))->assertOk();
        $this->get(route('products.index'))->assertOk();
        $this->get(route('products.low-stock'))->assertOk();
        $this->get(route('sales.create'))->assertOk();
        $this->get(route('purchases.create'))->assertOk();
    }

    public function test_staff_can_only_see_own_transactions(): void
    {
        $admin = $this->admin();
        $staff = $this->staff();
        $product = $this->product(['stock' => 20]);

        $this->actingAs($admin)->post(route('sales.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ]);
        $adminSale = Sale::firstOrFail();

        $this->actingAs($staff)->get(route('sales.show', $adminSale))->assertForbidden();
        $this->actingAs($staff)->get(route('sales.index'))->assertDontSee($adminSale->sale_number);
        $this->actingAs($admin)->get(route('sales.show', $adminSale))->assertOk();
    }

    public function test_admin_can_access_admin_pages(): void
    {
        $admin = $this->admin();
        $product = $this->product();

        $this->actingAs($admin);
        $this->get(route('products.create'))->assertOk();
        $this->get(route('products.edit', $product))->assertOk();
        $this->get(route('categories.index'))->assertOk();
        $this->get(route('suppliers.index'))->assertOk();
        $this->get(route('users.index'))->assertOk();
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->delete(route('users.destroy', $admin))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
