<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Systex Financeiro</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-zinc-950 font-sans text-white antialiased">
        <main class="min-h-screen overflow-hidden bg-[radial-gradient(circle_at_top_right,rgba(220,38,38,0.20),transparent_34%),linear-gradient(135deg,#09090b_0%,#18181b_52%,#09090b_100%)]">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-6">
                <a href="/" class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-red-600 font-black shadow-lg shadow-red-700/30">SX</span>
                    <span class="text-lg font-bold">Systex Financeiro</span>
                </a>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold shadow-lg shadow-red-700/25 transition hover:bg-red-500">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-zinc-300 transition hover:text-white">Login</a>
                        <a href="{{ route('register') }}" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold shadow-lg shadow-red-700/25 transition hover:bg-red-500">Começar</a>
                    @endauth
                </div>
            </nav>

            <section class="mx-auto grid min-h-[calc(100vh-92px)] max-w-7xl items-center gap-10 px-6 pb-16 pt-8 lg:grid-cols-[1fr_0.9fr]">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-red-400">SaaS financeiro simples</p>
                    <h1 class="mt-5 max-w-4xl text-5xl font-black leading-tight text-white sm:text-6xl lg:text-7xl">Systex Financeiro</h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-zinc-300">
                        Controle entradas, saídas, categorias e saldo mensal em uma experiência premium, direta e pronta para evoluir com sua operação.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" class="rounded-lg bg-red-600 px-6 py-3 text-sm font-bold shadow-xl shadow-red-700/25 transition hover:bg-red-500">Criar conta</a>
                        <a href="{{ route('login') }}" class="rounded-lg border border-white/15 px-6 py-3 text-sm font-bold text-zinc-200 transition hover:border-red-500/70 hover:text-white">Acessar plataforma</a>
                    </div>
                </div>

                <div class="rounded-xl border border-white/10 bg-black/45 p-5 shadow-2xl shadow-red-950/30 backdrop-blur">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-lg border border-emerald-400/20 bg-emerald-500/10 p-5">
                            <p class="text-sm text-emerald-200">Entradas</p>
                            <p class="mt-3 text-3xl font-black">R$ 18.420</p>
                        </div>
                        <div class="rounded-lg border border-red-400/20 bg-red-500/10 p-5">
                            <p class="text-sm text-red-200">Saídas</p>
                            <p class="mt-3 text-3xl font-black">R$ 9.870</p>
                        </div>
                        <div class="rounded-lg border border-white/10 bg-white/5 p-5 sm:col-span-2">
                            <p class="text-sm text-zinc-400">Saldo mensal</p>
                            <p class="mt-3 text-4xl font-black text-red-400">R$ 8.550</p>
                        </div>
                    </div>
                    <div class="mt-5 overflow-hidden rounded-lg border border-white/10">
                        <div class="grid grid-cols-[1fr_auto] border-b border-white/10 bg-white/5 px-4 py-3 text-sm font-semibold text-zinc-300">
                            <span>Últimos lançamentos</span>
                            <span>Valor</span>
                        </div>
                        <div class="divide-y divide-white/10 text-sm">
                            <div class="grid grid-cols-[1fr_auto] px-4 py-3"><span>Contrato mensal</span><span class="text-emerald-300">+ R$ 6.200</span></div>
                            <div class="grid grid-cols-[1fr_auto] px-4 py-3"><span>Infraestrutura</span><span class="text-red-300">- R$ 1.180</span></div>
                            <div class="grid grid-cols-[1fr_auto] px-4 py-3"><span>Consultoria</span><span class="text-emerald-300">+ R$ 3.900</span></div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
