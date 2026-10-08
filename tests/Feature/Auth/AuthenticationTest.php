<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_the_login_screen_reports_the_live_review_queue(): void
    {
        $this->seed();

        $this->get('/login')->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Auth/Login')
                ->where('queue.pending', 4)
                ->where('queue.frozen', 1),
        );
    }

    public function test_registration_is_disabled(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_the_seeded_demo_account_can_sign_in(): void
    {
        $this->seed();

        $response = $this->post('/login', [
            'email' => 'demo@syntavex.app',
            'password' => 'syntavex-demo',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
        $response->assertSessionHas('toast', [
            'tone' => 'success',
            'message' => 'Signed out. Enter the demo again anytime.',
        ]);
    }

    public function test_the_logout_toast_is_shared_once_on_the_next_page(): void
    {
        $this->seed();

        $this->actingAs(User::query()->firstOrFail())->post('/logout');

        $this->get('/')->assertInertia(
            fn (AssertableInertia $page) => $page
                ->where('flash.toast.tone', 'success')
                ->where('flash.toast.message', 'Signed out. Enter the demo again anytime.')
                ->has('flash.toast.id'),
        );

        $this->get('/')->assertInertia(
            fn (AssertableInertia $page) => $page->where('flash.toast', null),
        );
    }
}
