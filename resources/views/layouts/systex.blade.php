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
    <body class="bg-zinc-950 font-sans text-zinc-100 antialiased">
        <div class="min-h-screen lg:flex">
            <aside class="border-b border-white/10 bg-black/80 px-5 py-4 shadow-2xl shadow-red-950/20 lg:fixed lg:inset-y-0 lg:flex lg:w-72 lg:flex-col lg:border-b-0 lg:border-r">
                <div class="flex items-center justify-between lg:block">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-red-600 font-black text-white shadow-lg shadow-red-700/30">SX</span>
                        <span>
                            <span class="block text-sm font-semibold uppercase tracking-[0.22em] text-red-400">Systex</span>
                            <span class="block text-lg font-bold text-white">Financeiro</span>
                        </span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="lg:hidden">
                        @csrf
                        <button class="rounded-lg border border-white/10 px-3 py-2 text-sm text-zinc-300">Sair</button>
                    </form>
                </div>

                <nav class="mt-6 grid gap-2">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-red-600 text-white shadow-lg shadow-red-700/20' : 'text-zinc-300 hover:bg-white/5 hover:text-white' }} rounded-lg px-4 py-3 text-sm font-semibold transition">Dashboard</a>
                    <a href="{{ route('lancamentos.index') }}" class="{{ request()->routeIs('lancamentos.*') ? 'bg-red-600 text-white shadow-lg shadow-red-700/20' : 'text-zinc-300 hover:bg-white/5 hover:text-white' }} rounded-lg px-4 py-3 text-sm font-semibold transition">Lançamentos</a>
                    <a href="{{ route('categorias.index') }}" class="{{ request()->routeIs('categorias.*') ? 'bg-red-600 text-white shadow-lg shadow-red-700/20' : 'text-zinc-300 hover:bg-white/5 hover:text-white' }} rounded-lg px-4 py-3 text-sm font-semibold transition">Categorias</a>
                    <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'bg-red-600 text-white shadow-lg shadow-red-700/20' : 'text-zinc-300 hover:bg-white/5 hover:text-white' }} rounded-lg px-4 py-3 text-sm font-semibold transition">Perfil</a>
                </nav>

                <div class="mt-auto hidden border-t border-white/10 pt-6 lg:block">
                    <p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                    <p class="mt-1 truncate text-xs text-zinc-500">{{ auth()->user()->email }}</p>
                    <form method="POST" action="{{ route('logout') }}" class="mt-4">
                        @csrf
                        <button class="w-full rounded-lg border border-white/10 px-4 py-2 text-sm font-semibold text-zinc-300 transition hover:border-red-500/70 hover:text-white">Sair</button>
                    </form>
                </div>
            </aside>

            <div class="min-h-screen flex-1 lg:pl-72">
                <header class="border-b border-white/10 bg-zinc-950/80 px-5 py-6 backdrop-blur lg:px-10">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-red-400">{{ $eyebrow ?? 'MVP financeiro' }}</p>
                            <h1 class="mt-2 text-2xl font-bold text-white sm:text-3xl">{{ $heading ?? 'Painel Systex' }}</h1>
                        </div>

                        @isset($actions)
                            <div class="flex flex-wrap gap-3">{{ $actions }}</div>
                        @endisset
                    </div>
                </header>

                <main class="px-5 py-8 lg:px-10">
                    @if (session('success'))
                        <div class="mb-6 rounded-lg border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm font-semibold text-emerald-200">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 rounded-lg border border-red-400/30 bg-red-500/10 px-4 py-3 text-sm text-red-100">
                            <p class="font-semibold">Revise os campos destacados.</p>
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
