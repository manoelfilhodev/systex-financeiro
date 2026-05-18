<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PremiumMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless($user, 403);

        $user->normalizeSubscriptionState();

        if (! $user->hasPremiumAccess()) {
            return redirect()
                ->route('premium.index')
                ->with('success', 'Seu trial expirou. Você voltou para o plano Starter. Seus dados continuam seguros.');
        }

        return $next($request);
    }
}
