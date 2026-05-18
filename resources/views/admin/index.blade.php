@extends('layouts.systex', ['title' => 'Admin | Systex Financeiro', 'heading' => 'Admin financeiro', 'eyebrow' => 'Operação'])

@section('content')
    <div class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
        <section class="sx-card overflow-hidden">
            <div class="sx-divider border-b p-5">
                <h2 class="sx-theme-text text-lg font-black">Pagamentos pendentes</h2>
                <p class="sx-theme-muted mt-1 text-sm">Aprovação manual de comprovantes PIX.</p>
            </div>

            <div class="divide-y divide-white/[0.06]">
                @forelse ($pendingPayments as $payment)
                    <div class="p-5">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <p class="sx-theme-text font-black">{{ $payment->user->name }}</p>
                                <p class="sx-theme-muted text-sm">{{ $payment->user->email }} / {{ $payment->valor_formatado }}</p>
                                <p class="sx-theme-muted mt-1 text-xs">{{ $payment->comprovante_path }}</p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <form method="POST" action="{{ route('admin.payments.approve', $payment) }}">
                                    @csrf
                                    <button class="sx-button h-10 px-4 text-xs">Aprovar</button>
                                </form>
                                <form method="POST" action="{{ route('admin.payments.reject', $payment) }}">
                                    @csrf
                                    <button class="sx-button-danger h-10 px-4 text-xs">Rejeitar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center sx-theme-muted">Nenhum pagamento pendente.</div>
                @endforelse
            </div>
        </section>

        <section class="sx-card overflow-hidden">
            <div class="sx-divider border-b p-5">
                <h2 class="sx-theme-text text-lg font-black">Usuários</h2>
                <p class="sx-theme-muted mt-1 text-sm">Planos, trials e premium manual.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="sx-table">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Plano</th>
                            <th>Status</th>
                            <th>Trial</th>
                            <th>Premium</th>
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
                                <td>{{ ucfirst($user->plan) }}</td>
                                <td>{{ ucfirst($user->subscription_status) }}</td>
                                <td>{{ $user->trial_ends_at?->format('d/m/Y') ?? '-' }}</td>
                                <td>{{ $user->premium_until?->format('d/m/Y') ?? '-' }}</td>
                                <td>
                                    <div class="flex min-w-72 flex-col gap-2">
                                        <form method="POST" action="{{ route('admin.users.extend-trial', $user) }}" class="flex gap-2">
                                            @csrf
                                            <input name="days" type="number" min="1" max="90" value="7" class="sx-input h-10 w-20">
                                            <button class="sx-button-secondary h-10 px-3 text-xs">Trial</button>
                                        </form>
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
