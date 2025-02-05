<?php declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_view_pages_without_registration_confirmation(): void
    {
        $user = User::factory()->unconfirmed()->create();

        $response = $this->actingAs($user)->get(route('dashboard', absolute: false));
        $response->assertRedirect(route('registration.confirm', absolute: false));

        $response = $this->actingAs($user)->get(route('news', absolute: false));
        $response->assertRedirect(route('registration.confirm', absolute: false));

        $response = $this->actingAs($user)->get(route('profile.edit', absolute: false));
        $response->assertRedirect(route('registration.confirm', absolute: false));
    }
}
