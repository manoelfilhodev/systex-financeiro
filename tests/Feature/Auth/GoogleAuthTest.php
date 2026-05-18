<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_redirect_route_exists(): void
    {
        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(new RedirectResponse('https://accounts.google.com/o/oauth2/auth'));

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);

        $response = $this->get(route('google.redirect'));

        $response->assertRedirect('https://accounts.google.com/o/oauth2/auth');
    }

    public function test_google_callback_creates_new_user_with_premium_trial(): void
    {
        $this->mockGoogleUser([
            'id' => 'google-123',
            'name' => 'Google User',
            'email' => 'google@example.com',
            'avatar' => 'https://example.com/avatar.png',
        ]);

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticated();

        $user = User::where('email', 'google@example.com')->firstOrFail();

        $this->assertSame('google-123', $user->google_id);
        $this->assertSame('https://example.com/avatar.png', $user->avatar);
        $this->assertSame('google', $user->auth_provider);
        $this->assertSame('premium', $user->plan);
        $this->assertSame('trial', $user->subscription_status);
        $this->assertSame('systex-default', $user->theme);
        $this->assertTrue($user->trial_ends_at->isFuture());
    }

    public function test_google_callback_links_existing_user_by_email_without_changing_plan(): void
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'plan' => 'starter',
            'subscription_status' => 'expired',
        ]);

        $this->mockGoogleUser([
            'id' => 'google-existing',
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'avatar' => 'https://example.com/existing.png',
        ]);

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($user->fresh());

        $user->refresh();

        $this->assertSame('google-existing', $user->google_id);
        $this->assertSame('https://example.com/existing.png', $user->avatar);
        $this->assertSame('starter', $user->plan);
        $this->assertSame('expired', $user->subscription_status);
    }

    public function test_google_user_with_existing_google_id_can_authenticate(): void
    {
        $user = User::factory()->create([
            'email' => 'linked@example.com',
            'google_id' => 'google-linked',
            'auth_provider' => 'google',
        ]);

        $this->mockGoogleUser([
            'id' => 'google-linked',
            'name' => 'Linked User',
            'email' => 'linked@example.com',
        ]);

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($user);
    }

    public function test_google_callback_does_not_create_user_without_email(): void
    {
        $this->mockGoogleUser([
            'id' => 'google-no-email',
            'name' => 'No Email',
            'email' => null,
        ]);

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertDatabaseMissing('users', [
            'google_id' => 'google-no-email',
        ]);
    }

    /**
     * @param  array{id: string, name?: string|null, email?: string|null, avatar?: string|null}  $attributes
     */
    private function mockGoogleUser(array $attributes): void
    {
        $socialiteUser = (new SocialiteUser())->map([
            'id' => $attributes['id'],
            'name' => $attributes['name'] ?? null,
            'email' => $attributes['email'] ?? null,
            'avatar' => $attributes['avatar'] ?? null,
        ]);

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')
            ->once()
            ->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);
    }
}
