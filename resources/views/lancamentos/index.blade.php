@extends('layouts.systex', ['title' => 'Lançamentos | Systex Financeiro', 'heading' => 'Lançamentos financeiros', 'eyebrow' => 'Movimentações'])

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <form method="GET" action="{{ route('lancamentos.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div>
                <label for="mes" class="sx-label">Mês</label>
                <input id="mes" name="mes" type="month" value="{{ $mes }}" class="sx-input sm:w-48">
            </div>
            <button class="sx-button">Filtrar</button>
        </form>

        <a href="{{ route('lancamentos.create') }}" class="sx-button">Novo lançamento</a>
    </div>

    <section class="sx-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="sx-table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Descrição</th>
                        <th>Categoria</th>
                        <th>Tipo</th>
                        <th class="text-right">Valor</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lancamentos as $lancamento)
                        <tr class="text-zinc-300">
                            <td>{{ $lancamento->data_lancamento->format('d/m/Y') }}</td>
                            <td>
                                <p class="sx-theme-text font-bold">{{ $lancamento->descricao }}</p>
                                @if ($lancamento->observacao)
                                    <p class="mt-1 max-w-sm truncate text-xs text-zinc-500">{{ $lancamento->observacao }}</p>
                                @endif
                            </td>
                            <td>{{ $lancamento->categoria?->nome ?? 'Sem categoria' }}</td>
                            <td>
                                <span class="sx-badge {{ $lancamento->tipo === 'entrada' ? 'sx-badge-income' : 'sx-badge-expense' }}">{{ ucfirst($lancamento->tipo) }}</span>
                            </td>
                            <td class="text-right font-black {{ $lancamento->tipo === 'entrada' ? 'sx-value-income' : 'sx-value-expense' }}">{{ $lancamento->tipo === 'entrada' ? '+' : '-' }} {{ $lancamento->valor_formatado }}</td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('lancamentos.edit', $lancamento) }}" class="sx-button-secondary h-9 px-3 text-xs">Editar</a>
                                    <form method="POST" action="{{ route('lancamentos.destroy', $lancamento) }}" onsubmit="return confirm('Remover este lançamento?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="sx-button-danger h-9 px-3 text-xs">Remover</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-zinc-500">Nenhum lançamento encontrado para este mês.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="sx-divider border-t p-5">
            {{ $lancamentos->links() }}
        </div>
    </section>
@endsection
