@extends('layouts.systex', ['title' => 'Lançamentos | Systex Financeiro', 'heading' => 'Lançamentos financeiros', 'eyebrow' => 'Movimentações'])

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <form method="GET" action="{{ route('lancamentos.index') }}" class="flex flex-col gap-3 rounded-lg border border-white/10 bg-black/35 p-4 sm:flex-row sm:items-end">
            <div>
                <label for="mes" class="text-sm font-semibold text-zinc-300">Mês</label>
                <input id="mes" name="mes" type="month" value="{{ $mes }}" class="mt-2 rounded-lg border-white/10 bg-zinc-900 text-white focus:border-red-500 focus:ring-red-500">
            </div>
            <button class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-red-500">Filtrar</button>
        </form>

        <a href="{{ route('lancamentos.create') }}" class="rounded-lg bg-red-600 px-5 py-3 text-center text-sm font-bold text-white shadow-lg shadow-red-700/20 transition hover:bg-red-500">Novo lançamento</a>
    </div>

    <section class="rounded-lg border border-white/10 bg-black/35">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10 text-left text-sm">
                <thead class="bg-white/5 text-xs uppercase tracking-wide text-zinc-400">
                    <tr>
                        <th class="px-5 py-3">Data</th>
                        <th class="px-5 py-3">Descrição</th>
                        <th class="px-5 py-3">Categoria</th>
                        <th class="px-5 py-3">Tipo</th>
                        <th class="px-5 py-3 text-right">Valor</th>
                        <th class="px-5 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($lancamentos as $lancamento)
                        <tr class="text-zinc-200">
                            <td class="px-5 py-4">{{ $lancamento->data_lancamento->format('d/m/Y') }}</td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-white">{{ $lancamento->descricao }}</p>
                                @if ($lancamento->observacao)
                                    <p class="mt-1 max-w-sm truncate text-xs text-zinc-500">{{ $lancamento->observacao }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4">{{ $lancamento->categoria?->nome ?? 'Sem categoria' }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $lancamento->tipo === 'entrada' ? 'bg-emerald-500/15 text-emerald-200' : 'bg-red-500/15 text-red-200' }}">{{ ucfirst($lancamento->tipo) }}</span>
                            </td>
                            <td class="px-5 py-4 text-right font-bold {{ $lancamento->tipo === 'entrada' ? 'text-emerald-300' : 'text-red-300' }}">{{ $lancamento->tipo === 'entrada' ? '+' : '-' }} {{ $lancamento->valor_formatado }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('lancamentos.edit', $lancamento) }}" class="rounded-lg border border-white/10 px-3 py-2 text-xs font-bold text-zinc-200 transition hover:border-red-500/70 hover:text-white">Editar</a>
                                    <form method="POST" action="{{ route('lancamentos.destroy', $lancamento) }}" onsubmit="return confirm('Remover este lançamento?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg px-3 py-2 text-xs font-bold text-red-300 transition hover:bg-red-500/10 hover:text-red-200">Remover</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-zinc-400">Nenhum lançamento encontrado para este mês.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-white/10 p-5">
            {{ $lancamentos->links() }}
        </div>
    </section>
@endsection
