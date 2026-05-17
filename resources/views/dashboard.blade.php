@extends('layouts.systex', ['title' => 'Dashboard | Systex Financeiro', 'heading' => 'Dashboard financeiro', 'eyebrow' => 'Visão mensal'])

@section('content')
    <form method="GET" action="{{ route('dashboard') }}" class="mb-6 flex flex-col gap-3 rounded-lg border border-white/10 bg-black/35 p-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <label for="mes" class="text-sm font-semibold text-zinc-300">Mês de referência</label>
            <input id="mes" name="mes" type="month" value="{{ $mes }}" class="mt-2 rounded-lg border-white/10 bg-zinc-900 text-white focus:border-red-500 focus:ring-red-500">
        </div>
        <button class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-red-700/20 transition hover:bg-red-500">Filtrar</button>
    </form>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg border border-emerald-400/20 bg-emerald-500/10 p-5">
            <p class="text-sm font-semibold text-emerald-200">Entradas do mês</p>
            <p class="mt-3 text-3xl font-black text-white">R$ {{ number_format($totalEntradas, 2, ',', '.') }}</p>
        </div>
        <div class="rounded-lg border border-red-400/20 bg-red-500/10 p-5">
            <p class="text-sm font-semibold text-red-200">Saídas do mês</p>
            <p class="mt-3 text-3xl font-black text-white">R$ {{ number_format($totalSaidas, 2, ',', '.') }}</p>
        </div>
        <div class="rounded-lg border border-white/10 bg-white/5 p-5 shadow-xl shadow-red-950/15">
            <p class="text-sm font-semibold text-zinc-300">Saldo mensal</p>
            <p class="mt-3 text-3xl font-black {{ $saldoMes >= 0 ? 'text-emerald-300' : 'text-red-300' }}">R$ {{ number_format($saldoMes, 2, ',', '.') }}</p>
        </div>
        <div class="rounded-lg border border-white/10 bg-white/5 p-5">
            <p class="text-sm font-semibold text-zinc-300">Lançamentos</p>
            <p class="mt-3 text-3xl font-black text-white">{{ $quantidadeLancamentos }}</p>
        </div>
    </div>

    <section class="mt-8 rounded-lg border border-white/10 bg-black/35">
        <div class="flex flex-col gap-3 border-b border-white/10 p-5 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-bold text-white">Últimos lançamentos</h2>
            <a href="{{ route('lancamentos.create') }}" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-red-500">Novo lançamento</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10 text-left text-sm">
                <thead class="bg-white/5 text-xs uppercase tracking-wide text-zinc-400">
                    <tr>
                        <th class="px-5 py-3">Data</th>
                        <th class="px-5 py-3">Descrição</th>
                        <th class="px-5 py-3">Categoria</th>
                        <th class="px-5 py-3">Tipo</th>
                        <th class="px-5 py-3 text-right">Valor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($ultimosLancamentos as $lancamento)
                        <tr class="text-zinc-200">
                            <td class="px-5 py-4">{{ $lancamento->data_lancamento->format('d/m/Y') }}</td>
                            <td class="px-5 py-4 font-semibold text-white">{{ $lancamento->descricao }}</td>
                            <td class="px-5 py-4">{{ $lancamento->categoria?->nome ?? 'Sem categoria' }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $lancamento->tipo === 'entrada' ? 'bg-emerald-500/15 text-emerald-200' : 'bg-red-500/15 text-red-200' }}">{{ ucfirst($lancamento->tipo) }}</span>
                            </td>
                            <td class="px-5 py-4 text-right font-bold {{ $lancamento->tipo === 'entrada' ? 'text-emerald-300' : 'text-red-300' }}">{{ $lancamento->tipo === 'entrada' ? '+' : '-' }} {{ $lancamento->valor_formatado }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-zinc-400">Nenhum lançamento neste mês.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
