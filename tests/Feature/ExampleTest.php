<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /** Halaman root mengarahkan ke dashboard (yang lalu meminta login bagi tamu). */
    public function test_root_redirects_to_dashboard(): void
    {
        $this->get('/')->assertRedirect(route('dashboard'));
    }
}
