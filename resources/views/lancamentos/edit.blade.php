@extends('layouts.systex', ['title' => 'Editar lançamento | Systex Financeiro', 'heading' => 'Editar lançamento', 'eyebrow' => 'Movimentações'])

@section('content')
    <section class="sx-card max-w-3xl p-5 sm:p-6">
        <form method="POST" action="{{ route('lancamentos.update', $lancamento) }}" class="grid gap-5">
            @csrf
            @method('PUT')
            @include('lancamentos.form', ['lancamento' => $lancamento])

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('lancamentos.index') }}" class="sx-button-secondary">Cancelar</a>
                <button class="sx-button">Atualizar lançamento</button>
            </div>
        </form>
    </section>
@endsection
