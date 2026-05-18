@extends('layouts.systex', ['title' => 'Admin | Systex Financeiro', 'heading' => 'Admin financeiro', 'eyebrow' => 'Operação Systex'])

@php
    $money = fn ($value) => 'R$ '.number_format((float) $value, 2, ',', '.');

    $paymentBadges = [
        'pending' => 'bg-yellow-500/10 text-yellow-300 ring-yellow-500/20',
        'approved' => 'bg-emerald-500/10 text-emerald-300 ring-emerald-500/20',
        'rejected' => 'bg-red-500/10 text-red-300 ring-red-500/20',
    ];

    $planBadges = [
        'starter' => 'bg-zinc-500/10 text-zinc-300 ring-zinc-500/20',
        'premium' => 'sx-badge-theme',
        'admin' => 'bg-cyan-500/10 text-cyan-300 ring-cyan-500/20',
    ];

    $subscriptionBadges = [
        'trial' => 'bg-cyan-500/10 text-cyan-300 ring-cyan-500/20',
        'active' => 'sx-badge-theme',
        'expired' => 'bg-zinc-500/10 text-zinc-300 ring-zinc-500/20',
        'pending' => 'bg-yellow-500/10 text-yellow-300 ring-yellow-500/20',
        'cancelled' => 'bg-red-500/10 text-red-300 ring-red-500/20',
    ];

    $statCards = [
        ['label' => 'Total de usuários', 'value' => number_format($stats['total_users'], 0, ',', '.'), 'hint' => 'Base cadastrada'],
        ['label' => 'Usuários em trial', 'value' => number_format($stats['trial_users'], 0, ',', '.'), 'hint' => 'Experimentando premium'],
        ['label' => 'Usuários premium', 'value' => number_format($stats['premium_users'], 0, ',', '.'), 'hint' => 'Plano pago ou liberado'],
        ['label' => 'Usuários starter', 'value' => number_format($stats['starter_users'], 0, ',', '.'), 'hint' => 'Plano gratuito'],
        ['label' => 'Pagamentos pendentes', 'value' => number_format($stats['pending_payments'], 0, ',', '.'), 'hint' => 'Aguardando análise'],
        ['label' => 'Aprovados no mês', 'value' => number_format($stats['approved_payments_month'], 0, ',', '.'), 'hint' => 'Entradas confirmadas'],
        ['label' => 'Receita recebida', 'value' => $money($stats['revenue_month']), 'hint' => 'Aprovada no mês atual'],
        ['label' => 'Receita pendente', 'value' => $money($stats['pending_revenue']), 'hint' => 'PIX em análise'],
    ];
@endphp

@section('content')
    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($statCards as $card)
                <article class="sx-card sx-card-hover p-5">
                    <p class="sx-theme-muted text-xs font-bold uppercase tracking-[0.18em]">{{ $card['label'] }}</p>
                    <p class="sx-theme-text mt-4 text-2xl font-black">{{ $card['value'] }}</p>
                    <p class="sx-theme-muted mt-2 text-sm">{{ $card['hint'] }}</p>
                </article>
            @endforeach
        </section>

        <section class="sx-card overflow-hidden">
            <div class="sx-divider border-b p-5">
                <div class="flex flex-col gap-2 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h2 class="sx-theme-text text-lg font-black">Controle de entradas</h2>
                        <p class="sx-theme-muted mt-1 text-sm">Pagamentos PIX, receita operacional e ações rápidas de assinatura.</p>
                    </div>
                    <span class="sx-badge sx-badge-theme w-fit">{{ $payments->total() }} pagamentos</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="sx-table min-w-[1180px]">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Email</th>
                            <th>Plano</th>
                            <th>Status assinatura</th>
                            <th>Valor</th>
                            <th>Status pagamento</th>
                            <th>Solicitação</th>
                            <th>Aprovação</th>
                            <th>Premium até</th>
                            <th>Comprovante</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr>
                                <td>
                                    <p class="sx-theme-text font-bold">{{ $payment->user->name }}</p>
                                </td>
                                <td class="sx-theme-muted">{{ $payment->user->email }}</td>
                                <td>
                                    <span class="sx-badge ring-1 {{ $planBadges[$payment->user->plan] ?? 'bg-zinc-500/10 text-zinc-300 ring-zinc-500/20' }}">
                                        {{ ucfirst($payment->user->plan) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="sx-badge ring-1 {{ $subscriptionBadges[$payment->user->subscription_status] ?? 'bg-zinc-500/10 text-zinc-300 ring-zinc-500/20' }}">
                                        {{ ucfirst($payment->user->subscription_status) }}
                                    </span>
                                </td>
                                <td class="sx-theme-text font-black">{{ $payment->valor_formatado }}</td>
                                <td>
                                    <span class="sx-badge ring-1 {{ $paymentBadges[$payment->status] ?? 'bg-zinc-500/10 text-zinc-300 ring-zinc-500/20' }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="sx-theme-muted">{{ $payment->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td class="sx-theme-muted">
                                    {{ $payment->status === 'approved' ? $payment->updated_at?->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="sx-theme-muted">{{ $payment->user->premium_until?->format('d/m/Y') ?? '-' }}</td>
                                <td>
                                    @if ($payment->comprovante_path)
                                        <span class="sx-theme-muted block max-w-40 truncate text-xs" title="{{ $payment->comprovante_path }}">
                                            {{ basename($payment->comprovante_path) }}
                                        </span>
                                    @else
                                        <span class="sx-theme-muted text-xs">Sem arquivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex min-w-72 flex-wrap gap-2">
                                        @if ($payment->status === 'pending')
                                            <form method="POST" action="{{ route('admin.payments.approve', $payment) }}">
                                                @csrf
                                                <button class="sx-button h-9 px-3 text-xs">Aprovar</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.payments.reject', $payment) }}">
                                                @csrf
                                                <button class="sx-button-danger h-9 px-3 text-xs">Rejeitar</button>
                                            </form>
                                        @else
                                            <span class="sx-theme-muted flex h-9 items-center text-xs">Sem ação de pagamento</span>
                                        @endif

                                        <form method="POST" action="{{ route('admin.users.extend-premium', $payment->user) }}" class="flex gap-2">
                                            @csrf
                                            <input name="days" type="number" min="1" max="365" value="30" class="sx-input h-9 w-20 px-3 text-xs">
                                            <button class="sx-button-secondary h-9 px-3 text-xs">Premium</button>
                                        </form>

                                        @if (! $payment->user->isAdmin())
                                            <form method="POST" action="{{ route('admin.users.cancel-premium', $payment->user) }}">
                                                @csrf
                                                <button class="sx-button-danger h-9 px-3 text-xs">Cancelar</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="py-12 text-center sx-theme-muted">Nenhum pagamento registrado ainda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="sx-divider border-t p-5">
                {{ $payments->links() }}
            </div>
        </section>

        <section class="sx-card overflow-hidden">
            <div class="sx-divider border-b p-5">
                <h2 class="sx-theme-text text-lg font-black">Usuários</h2>
                <p class="sx-theme-muted mt-1 text-sm">Gestão manual de plano, trial e status de assinatura.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="sx-table min-w-[980px]">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Plano</th>
                            <th>Status</th>
                            <th>Trial</th>
                            <th>Premium</th>
                            <th>Pendências</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <p class="sx-theme-text font-bold">{{ $user->name }}</p>
                                    <p class="sx-theme-muted text-xs">{{ $user->email }}</p>
                                </td>
                                <td>
                                    <span class="sx-badge ring-1 {{ $planBadges[$user->plan] ?? 'bg-zinc-500/10 text-zinc-300 ring-zinc-500/20' }}">
                                        {{ ucfirst($user->plan) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="sx-badge ring-1 {{ $subscriptionBadges[$user->subscription_status] ?? 'bg-zinc-500/10 text-zinc-300 ring-zinc-500/20' }}">
                                        {{ ucfirst($user->subscription_status) }}
                                    </span>
                                </td>
                                <td class="sx-theme-muted">{{ $user->trial_ends_at?->format('d/m/Y') ?? '-' }}</td>
                                <td class="sx-theme-muted">{{ $user->premium_until?->format('d/m/Y') ?? '-' }}</td>
                                <td>
                                    <span class="sx-badge {{ $user->pending_payments_count > 0 ? 'bg-yellow-500/10 text-yellow-300' : 'bg-zinc-500/10 text-zinc-300' }}">
                                        {{ $user->pending_payments_count }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex min-w-[34rem] flex-col gap-2">
                                        <div class="grid gap-2 md:grid-cols-[auto_auto_auto]">
                                            <form method="POST" action="{{ route('admin.users.extend-trial', $user) }}" class="flex gap-2">
                                                @csrf
                                                <input name="days" type="number" min="1" max="90" value="7" class="sx-input h-10 w-20 px-3 text-xs">
                                                <button class="sx-button-secondary h-10 px-3 text-xs">Trial</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.users.extend-premium', $user) }}" class="flex gap-2">
                                                @csrf
                                                <input name="days" type="number" min="1" max="365" value="30" class="sx-input h-10 w-20 px-3 text-xs">
                                                <button class="sx-button-secondary h-10 px-3 text-xs">Premium</button>
                                            </form>
                                            @if (! $user->isAdmin())
                                                <form method="POST" action="{{ route('admin.users.cancel-premium', $user) }}">
                                                    @csrf
                                                    <button class="sx-button-danger h-10 px-3 text-xs">Cancelar premium</button>
                                                </form>
                                            @endif
                                        </div>

                                        <form method="POST" action="{{ route('admin.users.plan', $user) }}" class="grid grid-cols-[1fr_1fr_80px_auto] gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="plan" class="sx-select h-10 text-xs">
                                                @foreach (['starter', 'premium', 'admin'] as $plan)
                                                    <option value="{{ $plan }}" @selected($user->plan === $plan)>{{ ucfirst($plan) }}</option>
                                                @endforeach
                                            </select>
                                            <select name="subscription_status" class="sx-select h-10 text-xs">
                                                @foreach (['trial', 'active', 'expired', 'pending', 'cancelled'] as $status)
                                                    <option value="{{ $status }}" @selected($user->subscription_status === $status)>{{ ucfirst($status) }}</option>
                                                @endforeach
                                            </select>
                                            <input name="premium_days" type="number" min="0" max="365" placeholder="dias" class="sx-input h-10 text-xs">
                                            <button class="sx-button h-10 px-3 text-xs">Salvar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="sx-divider border-t p-5">
                {{ $users->links() }}
            </div>
        </section>
    </div>
@endsection
