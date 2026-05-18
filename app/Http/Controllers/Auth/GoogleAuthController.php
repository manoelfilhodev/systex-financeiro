<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            if (blank($googleUser->getEmail())) {
                return redirect()
                    ->route('login')
                    ->with('status', 'Não foi possível entrar com Google porque a conta não retornou um e-mail.');
            }

            $user = User::where('google_id', $googleUser->getId())->first();

            if (! $user) {
                $user = User::where('email', $googleUser->getEmail())->first();

                if ($user) {
                    if (filled($user->google_id) && $user->google_id !== $googleUser->getId()) {
                        return redirect()
                            ->route('login')
                            ->with('status', 'Este e-mail já está vinculado a outra conta Google.');
                    }

                    $user->forceFill([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar() ?: $user->avatar,
                    ])->save();
                }
            }

            if (! $user) {
                $user = User::create([
                    'name' => $googleUser->getName() ?: Str::before($googleUser->getEmail(), '@'),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'auth_provider' => 'google',
                    'password' => Hash::make(Str::random(48)),
                    'plan' => 'premium',
                    'subscription_status' => 'trial',
                    'trial_ends_at' => now()->addDays(15),
                    'theme' => 'systex-default',
                ]);
            }

            Auth::login($user, true);

            return redirect()->intended(route('dashboard', absolute: false));
        } catch (Throwable $exception) {
            Log::warning('Google authentication failed.', [
                'exception' => $exception::class,
            ]);

            return redirect()
                ->route('login')
                ->with('status', 'Não foi possível concluir o login com Google. Tente novamente.');
        }
    }
}
