<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesStoreData;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, CreatesStoreData;

    public function test_guest_cannot_open_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_guest_cannot_open_other_protected_pages(): void
    {
        $this->get(route('products.index'))->assertRedirect('/login');
        $this->get(route('sales.index'))->assertRedirect('/login');
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = $this->admin();

        $this->post('/login', ['email' => $user->email, 'password' => 'salah-total'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_valid_credentials_can_login_and_logout(): void
    {
        $user = $this->admin();

        $this->post('/login', ['email' => $user->email, 'password' => 'password123'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_password_is_stored_hashed(): void
    {
        $user = $this->admin();

        $this->assertNotSame('password123', $user->password);
        $this->assertTrue(password_verify('password123', $user->password));
    }
}
