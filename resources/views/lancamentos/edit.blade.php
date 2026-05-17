@extends('layouts.systex', ['title' => 'Editar lançamento | Systex Financeiro', 'heading' => 'Editar lançamento', 'eyebrow' => 'Movimentações'])

@section('content')
    <section class="max-w-3xl rounded-lg border border-white/10 bg-black/35 p-5">
        <form method="POST" action="{{ route('lancamentos.update', $lancamento) }}" class="grid gap-5">
            @csrf
            @method('PUT')
            @include('lancamentos.form', ['lancamento' => $lancamento])

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('lancamentos.index') }}" class="rounded-lg border border-white/10 px-5 py-3 text-center text-sm font-bold text-zinc-200 transition hover:border-red-500/70 hover:text-white">Cancelar</a>
                <button class="rounded-lg bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-red-700/20 transition hover:bg-red-500">Atualizar lançamento</button>
            </div>
        </form>
    </section>
@endsection
