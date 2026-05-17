<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label for="tipo" class="text-sm font-semibold text-zinc-300">Tipo</label>
        <select id="tipo" name="tipo" required class="mt-2 w-full rounded-lg border-white/10 bg-zinc-900 text-white focus:border-red-500 focus:ring-red-500">
            <option value="entrada" @selected(old('tipo', $lancamento?->tipo) === 'entrada')>Entrada</option>
            <option value="saida" @selected(old('tipo', $lancamento?->tipo) === 'saida')>Saída</option>
        </select>
        @error('tipo') <p class="mt-2 text-sm text-red-300">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="data_lancamento" class="text-sm font-semibold text-zinc-300">Data</label>
        <input id="data_lancamento" name="data_lancamento" type="date" value="{{ old('data_lancamento', $lancamento?->data_lancamento?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required class="mt-2 w-full rounded-lg border-white/10 bg-zinc-900 text-white focus:border-red-500 focus:ring-red-500">
        @error('data_lancamento') <p class="mt-2 text-sm text-red-300">{{ $message }}</p> @enderror
    </div>
</div>

<div>
    <label for="descricao" class="text-sm font-semibold text-zinc-300">Descrição</label>
    <input id="descricao" name="descricao" value="{{ old('descricao', $lancamento?->descricao) }}" required maxlength="180" class="mt-2 w-full rounded-lg border-white/10 bg-zinc-900 text-white focus:border-red-500 focus:ring-red-500">
    @error('descricao') <p class="mt-2 text-sm text-red-300">{{ $message }}</p> @enderror
</div>

<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label for="valor" class="text-sm font-semibold text-zinc-300">Valor</label>
        <input id="valor" name="valor" type="number" min="0.01" max="9999999999.99" step="0.01" value="{{ old('valor', $lancamento?->valor) }}" required class="mt-2 w-full rounded-lg border-white/10 bg-zinc-900 text-white focus:border-red-500 focus:ring-red-500">
        @error('valor') <p class="mt-2 text-sm text-red-300">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="categoria_id" class="text-sm font-semibold text-zinc-300">Categoria</label>
        <select id="categoria_id" name="categoria_id" class="mt-2 w-full rounded-lg border-white/10 bg-zinc-900 text-white focus:border-red-500 focus:ring-red-500">
            <option value="">Sem categoria</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}" @selected((string) old('categoria_id', $lancamento?->categoria_id) === (string) $categoria->id)>
                    {{ $categoria->nome }} / {{ ucfirst($categoria->tipo) }}
                </option>
            @endforeach
        </select>
        @error('categoria_id') <p class="mt-2 text-sm text-red-300">{{ $message }}</p> @enderror
    </div>
</div>

<div>
    <label for="observacao" class="text-sm font-semibold text-zinc-300">Observação</label>
    <textarea id="observacao" name="observacao" rows="4" class="mt-2 w-full rounded-lg border-white/10 bg-zinc-900 text-white focus:border-red-500 focus:ring-red-500">{{ old('observacao', $lancamento?->observacao) }}</textarea>
    @error('observacao') <p class="mt-2 text-sm text-red-300">{{ $message }}</p> @enderror
</div>
