<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_for_dashboard(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'username' => 'admin_test',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->post(route('login.attempt'), [
            'username' => 'admin_test',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'username' => 'visitor_test',
            'password' => bcrypt('good-pass'),
        ]);

        $response = $this->from(route('login'))->post(route('login.attempt'), [
            'username' => 'visitor_test',
            'password' => 'bad-pass',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['username']);
        $this->assertGuest();
    }

    public function test_login_is_temporarily_blocked_after_too_many_failed_attempts(): void
    {
        config()->set('security.login.max_attempts', 3);
        config()->set('security.login.lockout_seconds', 120);

        User::factory()->create([
            'username' => 'locked_user',
            'password' => bcrypt('good-pass'),
        ]);

        for ($i = 0; $i < 3; $i++) {
            $response = $this->from(route('login'))->post(route('login.attempt'), [
                'username' => 'locked_user',
                'password' => 'wrong-pass',
            ]);

            $response->assertRedirect(route('login'));
            $response->assertSessionHasErrors(['username']);
        }

        $blockedResponse = $this->from(route('login'))->post(route('login.attempt'), [
            'username' => 'locked_user',
            'password' => 'good-pass',
        ]);

        $blockedResponse->assertRedirect(route('login'));
        $blockedResponse->assertSessionHasErrors(['username']);
        $this->assertGuest();

        $this->travel(121)->seconds();

        $successResponse = $this->post(route('login.attempt'), [
            'username' => 'locked_user',
            'password' => 'good-pass',
        ]);

        $successResponse->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
