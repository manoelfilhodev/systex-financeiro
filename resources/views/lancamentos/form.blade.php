<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label for="tipo" class="sx-label">Tipo</label>
        <select id="tipo" name="tipo" required class="sx-select">
            <option value="entrada" @selected(old('tipo', $lancamento?->tipo) === 'entrada')>Entrada</option>
            <option value="saida" @selected(old('tipo', $lancamento?->tipo) === 'saida')>Saída</option>
        </select>
        @error('tipo') <p class="sx-theme-danger mt-2 text-sm font-semibold">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="data_lancamento" class="sx-label">Data</label>
        <input id="data_lancamento" name="data_lancamento" type="date" value="{{ old('data_lancamento', $lancamento?->data_lancamento?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required class="sx-input">
        @error('data_lancamento') <p class="sx-theme-danger mt-2 text-sm font-semibold">{{ $message }}</p> @enderror
    </div>
</div>

<div>
    <label for="descricao" class="sx-label">Descrição</label>
    <input id="descricao" name="descricao" value="{{ old('descricao', $lancamento?->descricao) }}" required maxlength="180" class="sx-input" placeholder="Ex: Assinatura cliente, aluguel, fornecedor">
    @error('descricao') <p class="sx-theme-danger mt-2 text-sm font-semibold">{{ $message }}</p> @enderror
</div>

<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label for="valor" class="sx-label">Valor</label>
        <input id="valor" name="valor" type="text" inputmode="numeric" autocomplete="off" value="{{ old('valor', $lancamento?->valor ? number_format((float) $lancamento->valor, 2, ',', '.') : '') }}" required class="sx-input" placeholder="0,00" data-currency-brl>
        @error('valor') <p class="sx-theme-danger mt-2 text-sm font-semibold">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="categoria_id" class="sx-label">Categoria</label>
        <select id="categoria_id" name="categoria_id" class="sx-select">
            <option value="">Sem categoria</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}" @selected((string) old('categoria_id', $lancamento?->categoria_id) === (string) $categoria->id)>
                    {{ $categoria->nome }} / {{ ucfirst($categoria->tipo) }}
                </option>
            @endforeach
        </select>
        @error('categoria_id') <p class="sx-theme-danger mt-2 text-sm font-semibold">{{ $message }}</p> @enderror
    </div>
</div>

<div>
    <label for="observacao" class="sx-label">Observação</label>
    <textarea id="observacao" name="observacao" rows="4" class="sx-textarea" placeholder="Detalhes opcionais sobre este lançamento">{{ old('observacao', $lancamento?->observacao) }}</textarea>
    @error('observacao') <p class="sx-theme-danger mt-2 text-sm font-semibold">{{ $message }}</p> @enderror
</div>
