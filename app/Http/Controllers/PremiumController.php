<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class PremiumController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $user->normalizeSubscriptionState();

        return view('premium.index', [
            'user' => $user,
            'payments' => $user->payments()->latest()->limit(5)->get(),
            'pixKey' => config('services.pix.key', env('PIX_KEY', 'financeiro@systex.com.br')),
            'premiumPrice' => 49.90,
        ]);
    }
}
