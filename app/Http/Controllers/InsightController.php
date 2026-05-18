<?php

namespace App\Http\Controllers;

use App\Models\Insight;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InsightController extends Controller
{
    public function read(Request $request, Insight $insight): RedirectResponse
    {
        abort_unless($insight->user_id === $request->user()->id, 403);

        $insight->update([
            'read_at' => now(),
        ]);

        return back()->with('success', 'Insight marcado como lido.');
    }

    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->insights()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'Insights marcados como lidos.');
    }
}
