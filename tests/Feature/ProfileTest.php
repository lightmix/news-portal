<?php declare(strict_types=1);

namespace Tests\Feature;

use App\Events\ProfileUpdated;
use App\Events\UserDeleted;
use App\Http\Middleware\ProtectGuestUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        Event::fake();

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);

        Event::assertDispatched(ProfileUpdated::class);
    }

    public function test_profile_information_cannot_be_updated_by_guest_user(): void
    {
        Event::fake();

        $user = $this->seedAndGetGuestUser();

        $oldUser = $user->replicate();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response->assertSessionHasErrors();

        $user->refresh();

        $this->assertSame($oldUser->name, $user->name);
        $this->assertSame($oldUser->email, $user->email);
        $this->assertTrue($oldUser->email_verified_at == $user->email_verified_at);

        Event::assertNotDispatched(ProfileUpdated::class);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_his_account(): void
    {
        Event::fake();

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());

        Event::assertDispatched(UserDeleted::class);
    }

    public function test_guest_user_cannot_delete_his_account(): void
    {
        Event::fake();

        $user = $this->seedAndGetGuestUser();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => '12345678',
            ]);

        $response->assertSessionHasErrors();

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas($user);

        Event::assertNotDispatched(UserDeleted::class);
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrors('password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
