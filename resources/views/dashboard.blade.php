@extends('layouts.systex', ['title' => 'Dashboard | Systex Financeiro', 'heading' => 'Resumo do mês', 'eyebrow' => 'Dashboard'])

@section('content')
    <div class="mb-6 flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
        <div>
            <p class="max-w-2xl text-sm leading-6 text-[#b5b5b5]">Acompanhe receitas, despesas, saldo e movimentações do período selecionado.</p>
        </div>

        <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <label for="mes" class="sr-only">Mês de referência</label>
            <input id="mes" name="mes" type="month" value="{{ $mes }}" class="sx-input sm:w-48">
            <button class="sx-button">Filtrar</button>
        </form>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Entradas', 'value' => $totalEntradas, 'tone' => 'text-emerald-300', 'meta' => 'Receitas do mês'],
            ['label' => 'Saídas', 'value' => $totalSaidas, 'tone' => 'text-red-300', 'meta' => 'Despesas do mês'],
            ['label' => 'Saldo', 'value' => $saldoMes, 'tone' => $saldoMes >= 0 ? 'text-emerald-300' : 'text-red-300', 'meta' => 'Entradas menos saídas'],
        ] as $card)
            <div class="sx-card sx-card-hover p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold text-zinc-500">{{ $card['label'] }}</p>
                        <p class="mt-3 text-3xl font-black {{ $card['tone'] }}">R$ {{ number_format($card['value'], 2, ',', '.') }}</p>
                        <p class="mt-3 text-xs font-semibold text-zinc-500">{{ $card['meta'] }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl border border-red-500/20 bg-red-500/10 text-[#ff2a2a]">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 6v12M6 12h12" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </span>
                </div>
            </div>
        @endforeach

        <div class="sx-card sx-card-hover p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-bold text-zinc-500">Total lançamentos</p>
                    <p class="mt-3 text-3xl font-black text-white">{{ $quantidadeLancamentos }}</p>
                    <p class="mt-3 text-xs font-semibold text-zinc-500">Movimentações no período</p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl border border-red-500/20 bg-red-500/10 text-[#ff2a2a]">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M7 7h10M7 12h10M7 17h6M4 4h16v16H4z" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.35fr_0.65fr]">
        <section class="sx-card overflow-hidden">
            <div class="flex flex-col gap-3 border-b border-white/[0.06] p-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-black text-white">Últimos lançamentos</h2>
                    <p class="mt-1 text-sm text-zinc-500">Movimentações mais recentes do mês filtrado.</p>
                </div>
                <a href="{{ route('lancamentos.create') }}" class="sx-button">Novo lançamento</a>
            </div>

            <div class="overflow-x-auto">
                <table class="sx-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Descrição</th>
                            <th>Categoria</th>
                            <th>Tipo</th>
                            <th class="text-right">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ultimosLancamentos as $lancamento)
                            <tr class="text-zinc-300">
                                <td>{{ $lancamento->data_lancamento->format('d/m/Y') }}</td>
                                <td class="font-bold text-white">{{ $lancamento->descricao }}</td>
                                <td>{{ $lancamento->categoria?->nome ?? 'Sem categoria' }}</td>
                                <td>
                                    <span class="sx-badge {{ $lancamento->tipo === 'entrada' ? 'bg-emerald-500/10 text-emerald-300' : 'bg-red-500/10 text-red-300' }}">{{ ucfirst($lancamento->tipo) }}</span>
                                </td>
                                <td class="text-right font-black {{ $lancamento->tipo === 'entrada' ? 'text-emerald-300' : 'text-red-300' }}">{{ $lancamento->tipo === 'entrada' ? '+' : '-' }} {{ $lancamento->valor_formatado }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-zinc-500">Nenhum lançamento neste mês.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="sx-card p-5">
            <h2 class="text-lg font-black text-white">Pulso financeiro</h2>
            <p class="mt-2 text-sm leading-6 text-zinc-500">Um resumo rápido para leitura executiva do mês.</p>
            <div class="mt-6 space-y-4">
                <div class="rounded-xl border border-white/[0.06] bg-white/[0.03] p-4">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-zinc-500">Margem do mês</p>
                    <p class="mt-2 text-2xl font-black {{ $saldoMes >= 0 ? 'text-emerald-300' : 'text-red-300' }}">
                        {{ $totalEntradas > 0 ? number_format(($saldoMes / max($totalEntradas, 1)) * 100, 1, ',', '.') : '0,0' }}%
                    </p>
                </div>
                <div class="rounded-xl border border-white/[0.06] bg-white/[0.03] p-4">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-zinc-500">Status</p>
                    <p class="mt-2 text-sm font-bold {{ $saldoMes >= 0 ? 'text-emerald-300' : 'text-red-300' }}">{{ $saldoMes >= 0 ? 'Operação positiva' : 'Atenção ao caixa' }}</p>
                </div>
            </div>
        </aside>
    </div>
@endsection
