<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ThemeController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $themes = array_keys(config('themes', []));

        $validated = $request->validate([
            'theme' => ['required', 'string', Rule::in($themes)],
        ]);

        $request->user()->update([
            'theme' => $validated['theme'],
        ]);

        return back()->with('success', 'Tema visual atualizado com sucesso.');
    }
}
