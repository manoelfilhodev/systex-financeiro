@php
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard', 'icon' => 'M4 13.5 12 5l8 8.5M6.5 11.5V20h11v-8.5'],
        ['label' => 'Lançamentos', 'route' => 'lancamentos.index', 'active' => 'lancamentos.*', 'icon' => 'M7 7h10M7 12h10M7 17h6M4 4h16v16H4z'],
        ['label' => 'Categorias', 'route' => 'categorias.index', 'active' => 'categorias.*', 'icon' => 'M4 7h6v6H4zM14 7h6v6h-6zM4 15h6v4H4zM14 15h6v4h-6z'],
        ['label' => 'Relatórios', 'route' => 'dashboard', 'active' => 'relatorios.*', 'icon' => 'M5 19V9m7 10V5m7 14v-7'],
        ['label' => 'Metas', 'route' => 'dashboard', 'active' => 'metas.*', 'icon' => 'M12 21a9 9 0 1 0-9-9m9 5a5 5 0 1 0-5-5m5 1h.01'],
        ['label' => 'Configurações', 'route' => 'profile.edit', 'active' => 'profile.*', 'icon' => 'M12 8a4 4 0 1 0 0 8a4 4 0 0 0 0-8zM4 12h2m12 0h2M12 4v2m0 12v2'],
    ];
    $initials = collect(explode(' ', auth()->user()->name))->filter()->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->join('');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Systex Financeiro' }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans">
        <div x-data="{ sidebarOpen: false, userMenuOpen: false }" class="sx-shell min-h-screen">
            <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-black/70 backdrop-blur-sm lg:hidden" x-on:click="sidebarOpen = false" style="display: none;"></div>

            <aside
                class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-white/[0.06] bg-[#0b0b0b]/95 shadow-2xl shadow-black/60 backdrop-blur-xl transition-transform duration-300 lg:translate-x-0"
                x-bind:class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': ! sidebarOpen }"
            >
                <div class="flex h-20 items-center justify-between border-b border-white/[0.06] px-6">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-b from-[#ff2a2a] to-[#b80000] font-black text-white shadow-lg shadow-red-500/25">S</span>
                        <span>
                            <span class="block text-base font-black leading-5 text-white">SYSTEX</span>
                            <span class="block text-[10px] font-bold uppercase tracking-[0.34em] text-[#ff2a2a]">Financeiro</span>
                        </span>
                    </a>

                    <button type="button" class="rounded-lg p-2 text-zinc-400 hover:bg-white/5 hover:text-white lg:hidden" x-on:click="sidebarOpen = false">
                        <span class="sr-only">Fechar menu</span>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="1.8" stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></svg>
                    </button>
                </div>

                <nav class="flex-1 space-y-1 px-4 py-6">
                    @foreach ($navItems as $item)
                        <a href="{{ route($item['route']) }}" class="{{ request()->routeIs($item['active']) ? 'border-red-500/30 bg-red-500/10 text-white shadow-lg shadow-red-500/10' : 'border-transparent text-zinc-400 hover:border-white/[0.06] hover:bg-white/[0.04] hover:text-white' }} group flex items-center gap-3 rounded-xl border px-4 py-3 text-sm font-semibold transition">
                            <svg class="h-5 w-5 {{ request()->routeIs($item['active']) ? 'text-[#ff2a2a]' : 'text-zinc-500 group-hover:text-[#ff2a2a]' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="{{ $item['icon'] }}" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="border-t border-white/[0.06] p-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="flex w-full items-center gap-3 rounded-xl border border-transparent px-4 py-3 text-sm font-semibold text-zinc-400 transition hover:border-white/[0.06] hover:bg-white/[0.04] hover:text-white">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M15 17l5-5-5-5M20 12H9M11 19H5V5h6" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Sair
                        </button>
                    </form>
                </div>
            </aside>

            <div class="lg:pl-72">
                <header class="sticky top-0 z-30 flex h-20 items-center justify-between border-b border-white/[0.06] bg-[#0b0b0b]/75 px-4 backdrop-blur-xl sm:px-6 lg:px-8">
                    <div class="flex items-center gap-4">
                        <button type="button" class="rounded-xl border border-white/[0.06] bg-white/[0.035] p-2.5 text-zinc-300 lg:hidden" x-on:click="sidebarOpen = true">
                            <span class="sr-only">Abrir menu</span>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="1.8" stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                        </button>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#ff2a2a]">{{ $eyebrow ?? 'Systex Financeiro' }}</p>
                            <h1 class="mt-1 text-xl font-black text-white sm:text-2xl">{{ $heading ?? 'Painel' }}</h1>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        @isset($actions)
                            <div class="hidden items-center gap-3 md:flex">{{ $actions }}</div>
                        @endisset

                        <button type="button" class="relative rounded-xl border border-white/[0.06] bg-white/[0.035] p-2.5 text-zinc-300 transition hover:border-red-500/30 hover:text-white">
                            <span class="absolute right-2.5 top-2.5 h-2 w-2 rounded-full bg-[#ff2a2a] shadow-lg shadow-red-500/50"></span>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0a3 3 0 0 1-6 0" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>

                        <div class="relative">
                            <button type="button" class="flex items-center gap-3 rounded-xl border border-white/[0.06] bg-white/[0.035] py-1.5 pl-1.5 pr-3 transition hover:border-red-500/30" x-on:click="userMenuOpen = ! userMenuOpen">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-[#ff2a2a] to-[#8f1111] text-sm font-black text-white">{{ $initials }}</span>
                                <span class="hidden text-left sm:block">
                                    <span class="block text-sm font-bold text-white">{{ auth()->user()->name }}</span>
                                    <span class="block max-w-36 truncate text-xs text-zinc-500">{{ auth()->user()->email }}</span>
                                </span>
                                <svg class="h-4 w-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="m6 9 6 6 6-6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>

                            <div x-show="userMenuOpen" x-transition x-on:click.outside="userMenuOpen = false" class="absolute right-0 mt-3 w-64 rounded-xl border border-white/[0.06] bg-[#111111] p-2 shadow-2xl shadow-black/60" style="display: none;">
                                <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-zinc-300 hover:bg-white/[0.04] hover:text-white">Meu perfil</a>
                                <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-zinc-300 hover:bg-white/[0.04] hover:text-white">Resumo financeiro</a>
                                <form method="POST" action="{{ route('logout') }}" class="mt-2 border-t border-white/[0.06] pt-2">
                                    @csrf
                                    <button class="block w-full rounded-lg px-3 py-2 text-left text-sm font-semibold text-red-300 hover:bg-red-500/10 hover:text-red-200">Sair</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                    @if (session('success'))
                        <div class="mb-6 rounded-xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm font-semibold text-emerald-200 shadow-lg shadow-emerald-950/20">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 rounded-xl border border-red-400/25 bg-red-500/10 px-4 py-3 text-sm text-red-100 shadow-lg shadow-red-950/20">
                            <p class="font-semibold">Revise os campos destacados.</p>
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
