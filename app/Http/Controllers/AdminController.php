<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        return view('admin.index', [
            'users' => User::query()
                ->withCount(['payments as pending_payments_count' => fn ($query) => $query->where('status', 'pending')])
                ->latest()
                ->paginate(20),
            'pendingPayments' => Payment::query()
                ->with('user')
                ->where('status', 'pending')
                ->latest()
                ->get(),
        ]);
    }

    public function approvePayment(Payment $payment): RedirectResponse
    {
        $payment->update(['status' => 'approved']);

        $premiumUntil = $payment->user->premium_until && $payment->user->premium_until->isFuture()
            ? $payment->user->premium_until->copy()->addMonth()
            : now()->addMonth();

        $payment->user->update([
            'plan' => 'premium',
            'subscription_status' => 'active',
            'premium_until' => $premiumUntil,
        ]);

        return back()->with('success', 'Pagamento aprovado e premium liberado.');
    }

    public function rejectPayment(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'observacao' => ['nullable', 'string', 'max:1000'],
        ]);

        $payment->update([
            'status' => 'rejected',
            'observacao' => $validated['observacao'] ?? $payment->observacao,
        ]);

        if (! $payment->user->hasPremiumAccess()) {
            $payment->user->update([
                'plan' => 'starter',
                'subscription_status' => 'expired',
            ]);
        }

        return back()->with('success', 'Pagamento rejeitado.');
    }

    public function extendTrial(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'days' => ['required', 'integer', 'min:1', 'max:90'],
        ]);

        $baseDate = $user->trial_ends_at && $user->trial_ends_at->isFuture()
            ? $user->trial_ends_at
            : now();

        $user->update([
            'plan' => 'premium',
            'subscription_status' => 'trial',
            'trial_ends_at' => $baseDate->copy()->addDays($validated['days']),
        ]);

        return back()->with('success', 'Trial estendido com sucesso.');
    }

    public function updateUserPlan(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'plan' => ['required', Rule::in(['starter', 'premium', 'admin'])],
            'subscription_status' => ['required', Rule::in(['trial', 'active', 'expired', 'pending', 'cancelled'])],
            'premium_days' => ['nullable', 'integer', 'min:0', 'max:365'],
        ]);

        $premiumUntil = ((int) ($validated['premium_days'] ?? 0)) > 0
            ? now()->addDays((int) $validated['premium_days'])
            : $user->premium_until;

        $user->update([
            'plan' => $validated['plan'],
            'subscription_status' => $validated['subscription_status'],
            'premium_until' => $premiumUntil,
            'theme' => $validated['plan'] === 'starter' ? 'systex-default' : $user->theme,
        ]);

        return back()->with('success', 'Plano do usuário atualizado.');
    }
}
