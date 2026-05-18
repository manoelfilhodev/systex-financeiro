<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ThemeController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->normalizeSubscriptionState();

        $themes = $user->hasPremiumAccess()
            ? array_keys(config('themes', []))
            : ['systex-default'];

        $validated = $request->validate([
            'theme' => ['required', 'string', Rule::in($themes)],
        ]);

        $user->update([
            'theme' => $validated['theme'],
        ]);

        return back()->with('success', 'Tema visual atualizado com sucesso.');
    }
}
