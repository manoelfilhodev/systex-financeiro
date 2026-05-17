@extends('layouts.systex', ['title' => 'Categorias | Systex Financeiro', 'heading' => 'Categorias financeiras', 'eyebrow' => 'Organização'])

@section('content')
    <div class="grid gap-6 xl:grid-cols-[420px_1fr]">
        <section class="sx-card p-5 sm:p-6">
            <h2 class="text-lg font-black text-white">Nova categoria</h2>
            <p class="mt-2 text-sm leading-6 text-zinc-500">Crie agrupadores para receitas, despesas ou ambos.</p>

            <form method="POST" action="{{ route('categorias.store') }}" class="mt-6 grid gap-5">
                @csrf
                <div>
                    <label for="nome" class="sx-label">Nome</label>
                    <input id="nome" name="nome" value="{{ old('nome') }}" required class="sx-input" placeholder="Ex: Receita recorrente">
                    @error('nome') <p class="mt-2 text-sm font-semibold text-red-300">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="tipo" class="sx-label">Tipo</label>
                    <select id="tipo" name="tipo" required class="sx-select">
                        <option value="ambos" @selected(old('tipo') === 'ambos')>Ambos</option>
                        <option value="entrada" @selected(old('tipo') === 'entrada')>Entrada</option>
                        <option value="saida" @selected(old('tipo') === 'saida')>Saída</option>
                    </select>
                    @error('tipo') <p class="mt-2 text-sm font-semibold text-red-300">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="cor" class="sx-label">Cor</label>
                    <input id="cor" name="cor" type="color" value="{{ old('cor', '#ff2a2a') }}" class="h-12 w-full rounded-lg border border-white/[0.08] bg-[#111111] p-1">
                    @error('cor') <p class="mt-2 text-sm font-semibold text-red-300">{{ $message }}</p> @enderror
                </div>

                <button class="sx-button">Criar categoria</button>
            </form>
        </section>

        <section class="sx-card overflow-hidden">
            <div class="border-b border-white/[0.06] p-5">
                <h2 class="text-lg font-black text-white">Minhas categorias</h2>
                <p class="mt-1 text-sm text-zinc-500">Edite rapidamente sem sair da página.</p>
            </div>

            <div class="divide-y divide-white/[0.06]">
                @forelse ($categorias as $categoria)
                    <div class="grid gap-4 p-5 lg:grid-cols-[1fr_auto] lg:items-center">
                        <form method="POST" action="{{ route('categorias.update', $categoria) }}" class="grid gap-3 sm:grid-cols-[1fr_150px_90px_auto] sm:items-center">
                            @csrf
                            @method('PUT')
                            <input name="nome" value="{{ old('nome', $categoria->nome) }}" required class="sx-input">
                            <select name="tipo" class="sx-select">
                                <option value="ambos" @selected($categoria->tipo === 'ambos')>Ambos</option>
                                <option value="entrada" @selected($categoria->tipo === 'entrada')>Entrada</option>
                                <option value="saida" @selected($categoria->tipo === 'saida')>Saída</option>
                            </select>
                            <input name="cor" type="color" value="{{ $categoria->cor ?? '#ff2a2a' }}" class="h-12 rounded-lg border border-white/[0.08] bg-[#111111] p-1">
                            <button class="sx-button-secondary">Salvar</button>
                        </form>

                        <div class="flex items-center justify-between gap-4 lg:justify-end">
                            <span class="text-sm font-semibold text-zinc-500">{{ $categoria->lancamentos_count }} lançamento(s)</span>
                            <form method="POST" action="{{ route('categorias.destroy', $categoria) }}" onsubmit="return confirm('Remover esta categoria? Os lançamentos ficarão sem categoria.');">
                                @csrf
                                @method('DELETE')
                                <button class="sx-button-danger">Remover</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-zinc-500">Nenhuma categoria criada ainda.</div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
