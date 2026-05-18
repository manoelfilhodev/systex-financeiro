@extends('layouts.systex', ['title' => 'Dashboard | Systex Financeiro', 'heading' => 'Resumo do mês', 'eyebrow' => 'Dashboard'])

@php
    $insightStyles = [
        'positive' => ['badge' => 'sx-badge-income', 'icon' => '+'],
        'warning' => ['badge' => 'sx-badge-expense', 'icon' => '!'],
        'achievement' => ['badge' => 'sx-badge-theme', 'icon' => '*'],
        'trend' => ['badge' => 'bg-cyan-500/10 text-cyan-300', 'icon' => '^'],
        'neutral' => ['badge' => 'bg-zinc-500/10 text-zinc-300', 'icon' => '-'],
    ];
@endphp

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
            ['label' => 'Entradas', 'value' => $totalEntradas, 'tone' => 'sx-value-income', 'meta' => 'Receitas do mês'],
            ['label' => 'Saídas', 'value' => $totalSaidas, 'tone' => 'sx-value-expense', 'meta' => 'Despesas do mês'],
            ['label' => 'Saldo', 'value' => $saldoMes, 'tone' => $saldoMes >= 0 ? 'sx-value-income' : 'sx-value-expense', 'meta' => 'Entradas menos saídas'],
        ] as $card)
            <div class="sx-card sx-card-hover p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold text-zinc-500">{{ $card['label'] }}</p>
                        <p class="mt-3 text-3xl font-black {{ $card['tone'] }}">R$ {{ number_format($card['value'], 2, ',', '.') }}</p>
                        <p class="mt-3 text-xs font-semibold text-zinc-500">{{ $card['meta'] }}</p>
                    </div>
                    <span class="sx-icon-box h-11 w-11">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 6v12M6 12h12" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </span>
                </div>
            </div>
        @endforeach

        <div class="sx-card sx-card-hover p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-bold text-zinc-500">Total lançamentos</p>
                    <p class="sx-theme-text mt-3 text-3xl font-black">{{ $quantidadeLancamentos }}</p>
                    <p class="mt-3 text-xs font-semibold text-zinc-500">Movimentações no período</p>
                </div>
                <span class="sx-icon-box h-11 w-11">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M7 7h10M7 12h10M7 17h6M4 4h16v16H4z" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
            </div>
        </div>
    </div>

    <section class="sx-card mt-6 overflow-hidden">
        <div class="sx-divider flex flex-col gap-3 border-b p-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="sx-theme-text text-lg font-black">Insights inteligentes</h2>
                <p class="sx-theme-muted mt-1 text-sm">Sinais automáticos do mês selecionado, gerados por regras simples de acompanhamento.</p>
            </div>

            @if ($insights->whereNull('read_at')->isNotEmpty())
                <form method="POST" action="{{ route('insights.read-all') }}">
                    @csrf
                    <button class="sx-button-secondary h-10 px-4 text-xs">Marcar todos como lidos</button>
                </form>
            @endif
        </div>

        <div class="grid gap-4 p-5 lg:grid-cols-2">
            @forelse ($insights as $insight)
                @php($style = $insightStyles[$insight->type] ?? $insightStyles['neutral'])

                <article class="sx-subcard p-4 {{ $insight->read_at ? 'opacity-70' : '' }}">
                    <div class="flex items-start gap-4">
                        <span class="sx-icon-box h-11 w-11 shrink-0 text-sm">{{ $style['icon'] }}</span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="sx-badge {{ $style['badge'] }}">{{ ucfirst($insight->type) }}</span>
                                @if ($insight->read_at)
                                    <span class="sx-theme-muted text-xs font-bold">Lido</span>
                                @endif
                            </div>
                            <h3 class="sx-theme-text mt-3 font-black">{{ $insight->title }}</h3>
                            <p class="sx-theme-muted mt-2 text-sm leading-6">{{ $insight->message }}</p>

                            @if (! $insight->read_at)
                                <form method="POST" action="{{ route('insights.read', $insight) }}" class="mt-4">
                                    @csrf
                                    <button class="sx-button-secondary h-9 px-3 text-xs">Marcar como lido</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="sx-subcard p-5 lg:col-span-2">
                    <p class="sx-theme-text font-bold">Ainda não há insights para este mês.</p>
                    <p class="sx-theme-muted mt-2 text-sm">Registre algumas movimentações e o painel começa a destacar sinais úteis automaticamente.</p>
                </div>
            @endforelse
        </div>

        @if ($showInsightUpgradeCta)
            <div class="sx-divider border-t p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="sx-theme-text font-black">Desbloqueie insights avançados no Premium.</p>
                        <p class="sx-theme-muted mt-1 text-sm">No Starter você vê uma prévia; no Premium todos os sinais do mês ficam disponíveis.</p>
                    </div>
                    <a href="{{ route('premium.index') }}" class="sx-button w-fit">Ver Premium</a>
                </div>
            </div>
        @endif
    </section>

    @if ($hasPremiumAccess)
        <script id="dashboard-charts-data" type="application/json">@json($chartData)</script>

        <div class="mt-6 grid gap-6 xl:grid-cols-[1.35fr_0.65fr]">
            <section class="sx-card sx-card-hover overflow-hidden p-5">
                <div class="mb-4 flex items-start justify-between gap-4">
                    <div>
                        <h2 class="sx-theme-text text-lg font-black">Fluxo financeiro</h2>
                        <p class="sx-theme-muted mt-1 text-sm">Entradas e saídas por dia no mês selecionado.</p>
                    </div>
                    <span class="sx-badge sx-badge-theme">Premium</span>
                </div>
                <div id="cashflowChart" class="min-h-[330px]"></div>
            </section>

            <section class="sx-card sx-card-hover overflow-hidden p-5">
                <div class="mb-4 flex items-start justify-between gap-4">
                    <div>
                        <h2 class="sx-theme-text text-lg font-black">Distribuição</h2>
                        <p class="sx-theme-muted mt-1 text-sm">Saídas agrupadas por categoria.</p>
                    </div>
                    <span class="sx-badge sx-badge-theme">Donut</span>
                </div>
                <div id="categoryChart" class="min-h-[330px]"></div>
            </section>

            <section class="sx-card sx-card-hover overflow-hidden p-5">
                <div class="mb-4 flex items-start justify-between gap-4">
                    <div>
                        <h2 class="sx-theme-text text-lg font-black">Evolução do saldo</h2>
                        <p class="sx-theme-muted mt-1 text-sm">Saldo acumulado dia a dia no período.</p>
                    </div>
                    <span class="sx-badge sx-badge-theme">Acumulado</span>
                </div>
                <div id="balanceChart" class="min-h-[330px]"></div>
            </section>

            <section class="sx-card sx-card-hover overflow-hidden p-5">
                <div class="mb-4 flex items-start justify-between gap-4">
                    <div>
                        <h2 class="sx-theme-text text-lg font-black">Saúde financeira</h2>
                        <p class="sx-theme-muted mt-1 text-sm">Margem: saldo dividido por entradas.</p>
                    </div>
                    <span class="sx-badge sx-badge-theme">Gauge</span>
                </div>
                <div id="healthGauge" class="min-h-[330px]"></div>
            </section>
        </div>
    @else
        <section class="sx-card mt-6 p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <span class="sx-badge sx-badge-theme">Starter</span>
                    <h2 class="sx-theme-text mt-4 text-2xl font-black">Seu trial expirou.</h2>
                    <p class="sx-theme-muted mt-2 max-w-2xl text-sm leading-6">Você voltou para o plano Starter. Seus dados continuam seguros. Os gráficos premium e todos os temas ficam disponíveis no Premium.</p>
                </div>
                <a href="{{ route('premium.index') }}" class="sx-button">Ver Premium</a>
            </div>
        </section>
    @endif

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.35fr_0.65fr]">
        <section class="sx-card overflow-hidden">
            <div class="sx-divider flex flex-col gap-3 border-b p-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="sx-theme-text text-lg font-black">Últimos lançamentos</h2>
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
                                <td class="sx-theme-text font-bold">{{ $lancamento->descricao }}</td>
                                <td>{{ $lancamento->categoria?->nome ?? 'Sem categoria' }}</td>
                                <td>
                                    <span class="sx-badge {{ $lancamento->tipo === 'entrada' ? 'sx-badge-income' : 'sx-badge-expense' }}">{{ ucfirst($lancamento->tipo) }}</span>
                                </td>
                                <td class="text-right font-black {{ $lancamento->tipo === 'entrada' ? 'sx-value-income' : 'sx-value-expense' }}">{{ $lancamento->tipo === 'entrada' ? '+' : '-' }} {{ $lancamento->valor_formatado }}</td>
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
            <h2 class="sx-theme-text text-lg font-black">Pulso financeiro</h2>
            <p class="mt-2 text-sm leading-6 text-zinc-500">Um resumo rápido para leitura executiva do mês.</p>
            <div class="mt-6 space-y-4">
                <div class="sx-subcard p-4">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-zinc-500">Margem do mês</p>
                    <p class="mt-2 text-2xl font-black {{ $saldoMes >= 0 ? 'sx-value-income' : 'sx-value-expense' }}">
                        {{ $totalEntradas > 0 ? number_format(($saldoMes / max($totalEntradas, 1)) * 100, 1, ',', '.') : '0,0' }}%
                    </p>
                </div>
                <div class="sx-subcard p-4">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-zinc-500">Status</p>
                    <p class="mt-2 text-sm font-bold {{ $saldoMes >= 0 ? 'sx-value-income' : 'sx-value-expense' }}">{{ $saldoMes >= 0 ? 'Operação positiva' : 'Atenção ao caixa' }}</p>
                </div>
            </div>
        </aside>
    </div>
@endsection
