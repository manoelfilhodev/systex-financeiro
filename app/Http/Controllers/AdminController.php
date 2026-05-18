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
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        return view('admin.index', [
            'stats' => [
                'total_users' => User::count(),
                'trial_users' => User::where('subscription_status', 'trial')->count(),
                'premium_users' => User::where('plan', 'premium')->count(),
                'starter_users' => User::where('plan', 'starter')->count(),
                'pending_payments' => Payment::where('status', 'pending')->count(),
                'approved_payments_month' => Payment::where('status', 'approved')
                    ->whereBetween('updated_at', [$monthStart, $monthEnd])
                    ->count(),
                'revenue_month' => Payment::where('status', 'approved')
                    ->whereBetween('updated_at', [$monthStart, $monthEnd])
                    ->sum('valor'),
                'pending_revenue' => Payment::where('status', 'pending')->sum('valor'),
            ],
            'payments' => Payment::query()
                ->with('user')
                ->latest()
                ->paginate(20, ['*'], 'payments_page'),
            'users' => User::query()
                ->withCount(['payments as pending_payments_count' => fn ($query) => $query->where('status', 'pending')])
                ->latest()
                ->paginate(15, ['*'], 'users_page'),
        ]);
    }

    public function approvePayment(Payment $payment): RedirectResponse
    {
        $payment->update(['status' => 'approved']);

        $premiumUntil = $payment->user->premium_until && $payment->user->premium_until->isFuture()
            ? $payment->user->premium_until->copy()->addDays(30)
            : now()->addDays(30);

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

        return back()->with('success', 'Pagamento rejeitado.');
    }

    public function extendPremium(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'days' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        $baseDate = $user->premium_until && $user->premium_until->isFuture()
            ? $user->premium_until
            : now();

        $user->update([
            'plan' => 'premium',
            'subscription_status' => 'active',
            'premium_until' => $baseDate->copy()->addDays((int) $validated['days']),
        ]);

        return back()->with('success', 'Premium estendido com sucesso.');
    }

    public function cancelPremium(User $user): RedirectResponse
    {
        if ($user->isAdmin()) {
            return back()->withErrors(['user' => 'Administradores não podem ter o premium cancelado por esta ação.']);
        }

        $user->update([
            'plan' => 'starter',
            'subscription_status' => 'cancelled',
            'premium_until' => null,
            'theme' => 'systex-default',
        ]);

        return back()->with('success', 'Premium cancelado. Os dados do usuário foram preservados.');
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
