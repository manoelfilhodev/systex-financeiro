@extends('layouts.systex', ['title' => 'Novo lançamento | Systex Financeiro', 'heading' => 'Novo lançamento', 'eyebrow' => 'Movimentações'])

@section('content')
    <section class="sx-card max-w-3xl p-5 sm:p-6">
        <form method="POST" action="{{ route('lancamentos.store') }}" class="grid gap-5">
            @csrf
            @include('lancamentos.form', ['lancamento' => null])

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('lancamentos.index') }}" class="sx-button-secondary">Cancelar</a>
                <button class="sx-button">Salvar lançamento</button>
            </div>
        </form>
    </section>
@endsection
