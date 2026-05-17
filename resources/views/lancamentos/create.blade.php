@extends('layouts.systex', ['title' => 'Novo lançamento | Systex Financeiro', 'heading' => 'Novo lançamento', 'eyebrow' => 'Movimentações'])

@section('content')
    <section class="max-w-3xl rounded-lg border border-white/10 bg-black/35 p-5">
        <form method="POST" action="{{ route('lancamentos.store') }}" class="grid gap-5">
            @csrf
            @include('lancamentos.form', ['lancamento' => null])

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('lancamentos.index') }}" class="rounded-lg border border-white/10 px-5 py-3 text-center text-sm font-bold text-zinc-200 transition hover:border-red-500/70 hover:text-white">Cancelar</a>
                <button class="rounded-lg bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-red-700/20 transition hover:bg-red-500">Salvar lançamento</button>
            </div>
        </form>
    </section>
@endsection
