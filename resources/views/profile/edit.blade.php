@php
    $initials = collect(explode(' ', $user->name))->filter()->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->join('');
    $themes = config('themes', []);
    $currentTheme = array_key_exists($user->theme, $themes) ? $user->theme : 'systex-default';
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

            <section class="sx-card p-5 sm:p-6">
                <header class="mb-6">
                    <h2 class="sx-theme-text text-lg font-black">Aparência</h2>
                    <p class="sx-theme-muted mt-2 text-sm leading-6">Escolha o tema visual que será aplicado automaticamente em toda a área logada.</p>
                </header>

                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($themes as $themeKey => $theme)
                        <form method="POST" action="{{ route('theme.update') }}" class="sx-card sx-card-hover p-4">
                            @csrf
                            <input type="hidden" name="theme" value="{{ $themeKey }}">

                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="sx-theme-text font-black">{{ $theme['name'] }}</h3>
                                    <p class="sx-theme-muted mt-2 text-sm leading-5">{{ $theme['description'] }}</p>
                                </div>

                                @if ($currentTheme === $themeKey)
                                    <span class="sx-badge sx-badge-theme">Atual</span>
                                @endif
                            </div>

                            <div class="mt-5 flex items-center justify-between gap-4">
                                <div class="flex -space-x-2">
                                    @foreach ($theme['colors'] as $color)
                                        <span class="h-8 w-8 rounded-full border border-white/20 shadow-lg" style="background: {{ $color }}"></span>
                                    @endforeach
                                </div>

                                <button class="{{ $currentTheme === $themeKey ? 'sx-button-secondary' : 'sx-button' }} h-10 px-4 text-xs">
                                    {{ $currentTheme === $themeKey ? 'Aplicado' : 'Aplicar' }}
                                </button>
                            </div>
                        </form>
                    @endforeach
                </div>
            </section>

            <section class="sx-card p-5 sm:p-6">
                @include('profile.partials.delete-user-form')
            </section>
        </div>

        <aside class="sx-card h-fit p-6 text-center">
            <div class="mx-auto flex h-28 w-28 items-center justify-center rounded-full text-4xl font-black text-white shadow-2xl" style="background: linear-gradient(135deg, var(--sx-primary), var(--sx-primary-strong)); box-shadow: 0 18px 50px var(--sx-primary-glow);">
                {{ $initials }}
            </div>
            <h2 class="sx-theme-text mt-5 text-xl font-black">{{ $user->name }}</h2>
            <p class="sx-theme-muted mt-1 break-all text-sm">{{ $user->email }}</p>
            <div class="sx-theme-border mt-6 rounded-xl border p-4 text-left">
                <p class="sx-theme-muted text-xs font-bold uppercase tracking-[0.16em]">Conta</p>
                <p class="mt-2 text-sm font-semibold text-emerald-300">Ativa e protegida</p>
            </div>
            <div class="sx-theme-border mt-4 rounded-xl border p-4 text-left">
                <p class="sx-theme-muted text-xs font-bold uppercase tracking-[0.16em]">Tema atual</p>
                <p class="sx-theme-primary mt-2 text-sm font-black">{{ $themes[$currentTheme]['name'] ?? 'Systex Default' }}</p>
            </div>
        </aside>
    </div>
@endsection
