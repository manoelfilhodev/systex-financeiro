@php
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard', 'icon' => 'M4 13.5 12 5l8 8.5M6.5 11.5V20h11v-8.5'],
        ['label' => 'Lançamentos', 'route' => 'lancamentos.index', 'active' => 'lancamentos.*', 'icon' => 'M7 7h10M7 12h10M7 17h6M4 4h16v16H4z'],
        ['label' => 'Categorias', 'route' => 'categorias.index', 'active' => 'categorias.*', 'icon' => 'M4 7h6v6H4zM14 7h6v6h-6zM4 15h6v4H4zM14 15h6v4h-6z'],
        ['label' => 'Relatórios', 'route' => 'dashboard', 'active' => 'relatorios.*', 'icon' => 'M5 19V9m7 10V5m7 14v-7'],
        ['label' => 'Metas', 'route' => 'dashboard', 'active' => 'metas.*', 'icon' => 'M12 21a9 9 0 1 0-9-9m9 5a5 5 0 1 0-5-5m5 1h.01'],
        ['label' => 'Premium', 'route' => 'premium.index', 'active' => 'premium.*', 'icon' => 'M12 3l2.7 5.47 6.03.88-4.36 4.25 1.03 6L12 16.77 6.6 19.6l1.03-6-4.36-4.25 6.03-.88z'],
        ['label' => 'Configurações', 'route' => 'profile.edit', 'active' => 'profile.*', 'icon' => 'M12 8a4 4 0 1 0 0 8a4 4 0 0 0 0-8zM4 12h2m12 0h2M12 4v2m0 12v2'],
    ];
    if (auth()->user()->isAdmin()) {
        $navItems[] = ['label' => 'Admin', 'route' => 'admin.index', 'active' => 'admin.*', 'icon' => 'M12 3l8 4v5c0 5-3.4 8.5-8 9-4.6-.5-8-4-8-9V7z'];
    }
    $themes = config('themes', []);
    $currentTheme = array_key_exists(auth()->user()->theme, $themes) ? auth()->user()->theme : 'systex-default';
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
    <body class="theme-{{ $currentTheme }} font-sans">
        <div x-data="{ sidebarOpen: false, userMenuOpen: false }" class="sx-shell min-h-screen">
            <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 backdrop-blur-sm lg:hidden" style="display: none; background: color-mix(in srgb, var(--sx-bg) 72%, black);" x-on:click="sidebarOpen = false"></div>

            <aside
                class="sx-sidebar fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r transition-transform duration-300 lg:translate-x-0"
                x-bind:class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': ! sidebarOpen }"
            >
                <div class="sx-theme-border flex h-20 items-center justify-between border-b px-6">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <span class="sx-logo-mark h-11 w-11">S</span>
                        <span>
                            <span class="sx-theme-text block text-base font-black leading-5">SYSTEX</span>
                            <span class="sx-theme-primary block text-[10px] font-bold uppercase tracking-[0.34em]">Financeiro</span>
                        </span>
                    </a>

                    <button type="button" class="sx-button-secondary h-10 w-10 p-0 lg:hidden" x-on:click="sidebarOpen = false">
                        <span class="sr-only">Fechar menu</span>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="1.8" stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></svg>
                    </button>
                </div>

                <nav class="flex-1 space-y-1 px-4 py-6">
                    @foreach ($navItems as $item)
                        <a href="{{ route($item['route']) }}" class="sx-nav-item {{ request()->routeIs($item['active']) ? 'sx-nav-item-active' : '' }}">
                            <svg class="sx-nav-icon h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="{{ $item['icon'] }}" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="sx-theme-border border-t p-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="sx-nav-item w-full">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M15 17l5-5-5-5M20 12H9M11 19H5V5h6" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Sair
                        </button>
                    </form>
                </div>
            </aside>

            <div class="lg:pl-72">
                <header class="sx-topbar sticky top-0 z-30 flex h-20 items-center justify-between border-b px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-4">
                        <button type="button" class="sx-button-secondary h-11 w-11 p-0 lg:hidden" x-on:click="sidebarOpen = true">
                            <span class="sr-only">Abrir menu</span>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="1.8" stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                        </button>
                        <div>
                            <p class="sx-theme-primary text-xs font-bold uppercase tracking-[0.22em]">{{ $eyebrow ?? 'Systex Financeiro' }}</p>
                            <h1 class="sx-theme-text mt-1 text-xl font-black sm:text-2xl">{{ $heading ?? 'Painel' }}</h1>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        @isset($actions)
                            <div class="hidden items-center gap-3 md:flex">{{ $actions }}</div>
                        @endisset

                        <span class="sx-theme-debug">
                            Tema atual: {{ auth()->user()->theme ?? 'systex-default' }}
                        </span>

                        <button type="button" class="sx-button-secondary relative h-11 w-11 p-0">
                            <span class="absolute right-2.5 top-2.5 h-2 w-2 rounded-full shadow-lg" style="background: var(--sx-primary); box-shadow: 0 0 18px var(--sx-primary-glow);"></span>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0a3 3 0 0 1-6 0" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>

                        <div class="relative">
                            <button type="button" class="sx-button-secondary h-auto py-1.5 pl-1.5 pr-3" x-on:click="userMenuOpen = ! userMenuOpen">
                                <span class="sx-avatar h-9 w-9 text-sm">{{ $initials }}</span>
                                <span class="hidden text-left sm:block">
                                    <span class="sx-theme-text block text-sm font-bold">{{ auth()->user()->name }}</span>
                                    <span class="sx-theme-muted block max-w-36 truncate text-xs">{{ auth()->user()->email }}</span>
                                </span>
                                <svg class="sx-theme-muted h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="m6 9 6 6 6-6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>

                            <div x-show="userMenuOpen" x-transition x-on:click.outside="userMenuOpen = false" class="sx-panel absolute right-0 mt-3 w-72 rounded-xl p-2" style="display: none;">
                                <a href="{{ route('profile.edit') }}" class="sx-nav-item px-3 py-2">Meu perfil</a>
                                <a href="{{ route('dashboard') }}" class="sx-nav-item px-3 py-2">Resumo financeiro</a>
                                <form method="POST" action="{{ route('theme.update') }}" class="sx-theme-border mt-2 border-t px-3 py-3">
                                    @csrf
                                    <label for="topbar-theme" class="sx-label">Tema</label>
                                    <div class="flex gap-2">
                                        <select id="topbar-theme" name="theme" class="sx-select h-10 text-xs">
                                            @foreach ($themes as $themeKey => $theme)
                                                <option value="{{ $themeKey }}" @selected($currentTheme === $themeKey)>{{ $theme['name'] }}</option>
                                            @endforeach
                                        </select>
                                        <button class="sx-button h-10 px-3 text-xs">OK</button>
                                    </div>
                                </form>
                                <form method="POST" action="{{ route('logout') }}" class="sx-theme-border mt-2 border-t pt-2">
                                    @csrf
                                    <button class="sx-nav-item w-full px-3 py-2 text-left">Sair</button>
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
                        <div class="sx-card mb-6 px-4 py-3 text-sm sx-theme-danger">
                            <p class="font-semibold">Revise os campos destacados.</p>
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
