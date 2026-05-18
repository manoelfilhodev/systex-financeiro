<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'comprovante' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'observacao' => ['nullable', 'string', 'max:1000'],
        ]);

        $path = $request->file('comprovante')->store('payments');

        Payment::create([
            'user_id' => $request->user()->id,
            'valor' => 49.90,
            'status' => 'pending',
            'comprovante_path' => $path,
            'observacao' => $validated['observacao'] ?? null,
        ]);

        $request->user()->update([
            'subscription_status' => 'pending',
        ]);

        return back()->with('success', 'Comprovante enviado. Vamos revisar seu pagamento manualmente.');
    }
}
