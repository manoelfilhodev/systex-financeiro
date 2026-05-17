@php
    $initials = collect(explode(' ', $user->name))->filter()->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->join('');
@endphp

@extends('layouts.systex', ['title' => 'Meu perfil | Systex Financeiro', 'heading' => 'Meu perfil', 'eyebrow' => 'Configurações'])

@section('content')
    <div class="grid gap-6 xl:grid-cols-[1fr_360px]">
        <div class="space-y-6">
            <section class="sx-card p-5 sm:p-6">
                @include('profile.partials.update-profile-information-form')
            </section>

            <section class="sx-card p-5 sm:p-6">
                @include('profile.partials.update-password-form')
            </section>

            <section class="sx-card border-red-500/20 p-5 sm:p-6">
                @include('profile.partials.delete-user-form')
            </section>
        </div>

        <aside class="sx-card h-fit p-6 text-center">
            <div class="mx-auto flex h-28 w-28 items-center justify-center rounded-full bg-gradient-to-br from-[#ff2a2a] to-[#8f1111] text-4xl font-black text-white shadow-2xl shadow-red-500/20">
                {{ $initials }}
            </div>
            <h2 class="mt-5 text-xl font-black text-white">{{ $user->name }}</h2>
            <p class="mt-1 break-all text-sm text-zinc-500">{{ $user->email }}</p>
            <div class="mt-6 rounded-xl border border-white/[0.06] bg-white/[0.03] p-4 text-left">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-zinc-500">Conta</p>
                <p class="mt-2 text-sm font-semibold text-emerald-300">Ativa e protegida</p>
            </div>
        </aside>
    </div>
@endsection
