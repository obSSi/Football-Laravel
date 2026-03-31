<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
    }

    public function test_global_dos_limiter_blocks_excessive_requests(): void
    {
        config()->set('security.dos.max_requests_per_minute', 2);

        $this->get('/login')->assertOk();
        $this->get('/login')->assertOk();
        $this->get('/login')->assertStatus(429);
    }
}
