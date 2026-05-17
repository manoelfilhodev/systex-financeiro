@extends('layouts.systex', ['title' => 'Categorias | Systex Financeiro', 'heading' => 'Categorias financeiras', 'eyebrow' => 'Organização'])

@section('content')
    <div class="grid gap-6 xl:grid-cols-[420px_1fr]">
        <section class="rounded-lg border border-white/10 bg-black/35 p-5">
            <h2 class="text-lg font-bold text-white">Nova categoria</h2>

            <form method="POST" action="{{ route('categorias.store') }}" class="mt-5 grid gap-4">
                @csrf
                <div>
                    <label for="nome" class="text-sm font-semibold text-zinc-300">Nome</label>
                    <input id="nome" name="nome" value="{{ old('nome') }}" required class="mt-2 w-full rounded-lg border-white/10 bg-zinc-900 text-white focus:border-red-500 focus:ring-red-500">
                    @error('nome') <p class="mt-2 text-sm text-red-300">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="tipo" class="text-sm font-semibold text-zinc-300">Tipo</label>
                    <select id="tipo" name="tipo" required class="mt-2 w-full rounded-lg border-white/10 bg-zinc-900 text-white focus:border-red-500 focus:ring-red-500">
                        <option value="ambos" @selected(old('tipo') === 'ambos')>Ambos</option>
                        <option value="entrada" @selected(old('tipo') === 'entrada')>Entrada</option>
                        <option value="saida" @selected(old('tipo') === 'saida')>Saída</option>
                    </select>
                    @error('tipo') <p class="mt-2 text-sm text-red-300">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="cor" class="text-sm font-semibold text-zinc-300">Cor</label>
                    <input id="cor" name="cor" type="color" value="{{ old('cor', '#dc2626') }}" class="mt-2 h-11 w-full rounded-lg border border-white/10 bg-zinc-900 p-1">
                    @error('cor') <p class="mt-2 text-sm text-red-300">{{ $message }}</p> @enderror
                </div>

                <button class="rounded-lg bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-red-700/20 transition hover:bg-red-500">Criar categoria</button>
            </form>
        </section>

        <section class="rounded-lg border border-white/10 bg-black/35">
            <div class="border-b border-white/10 p-5">
                <h2 class="text-lg font-bold text-white">Minhas categorias</h2>
            </div>

            <div class="divide-y divide-white/10">
                @forelse ($categorias as $categoria)
                    <div class="grid gap-4 p-5 lg:grid-cols-[1fr_auto] lg:items-center">
                        <form method="POST" action="{{ route('categorias.update', $categoria) }}" class="grid gap-3 sm:grid-cols-[1fr_150px_90px_auto] sm:items-center">
                            @csrf
                            @method('PUT')
                            <input name="nome" value="{{ old('nome', $categoria->nome) }}" required class="rounded-lg border-white/10 bg-zinc-900 text-white focus:border-red-500 focus:ring-red-500">
                            <select name="tipo" class="rounded-lg border-white/10 bg-zinc-900 text-white focus:border-red-500 focus:ring-red-500">
                                <option value="ambos" @selected($categoria->tipo === 'ambos')>Ambos</option>
                                <option value="entrada" @selected($categoria->tipo === 'entrada')>Entrada</option>
                                <option value="saida" @selected($categoria->tipo === 'saida')>Saída</option>
                            </select>
                            <input name="cor" type="color" value="{{ $categoria->cor ?? '#dc2626' }}" class="h-10 rounded-lg border border-white/10 bg-zinc-900 p-1">
                            <button class="rounded-lg border border-white/10 px-4 py-2 text-sm font-bold text-zinc-200 transition hover:border-red-500/70 hover:text-white">Salvar</button>
                        </form>

                        <div class="flex items-center justify-between gap-4 lg:justify-end">
                            <span class="text-sm text-zinc-500">{{ $categoria->lancamentos_count }} lançamento(s)</span>
                            <form method="POST" action="{{ route('categorias.destroy', $categoria) }}" onsubmit="return confirm('Remover esta categoria? Os lançamentos ficarão sem categoria.');">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-lg px-3 py-2 text-sm font-bold text-red-300 transition hover:bg-red-500/10 hover:text-red-200">Remover</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-zinc-400">Nenhuma categoria criada ainda.</div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
