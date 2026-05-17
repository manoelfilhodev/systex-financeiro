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
        <main class="sx-shell min-h-screen overflow-hidden">
            <div class="mx-auto grid min-h-screen max-w-7xl lg:grid-cols-[0.95fr_1.05fr]">
                <section class="relative hidden border-r border-white/[0.06] px-10 py-10 lg:flex lg:flex-col">
                    <a href="/" class="flex items-center gap-3">
                        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-b from-[#ff2a2a] to-[#b80000] font-black text-white shadow-lg shadow-red-500/25">S</span>
                        <span>
                            <span class="block text-lg font-black leading-5 text-white">SYSTEX</span>
                            <span class="block text-[10px] font-bold uppercase tracking-[0.36em] text-[#ff2a2a]">Financeiro</span>
                        </span>
                    </a>

                    <div class="my-auto max-w-xl">
                        <p class="text-sm font-bold uppercase tracking-[0.28em] text-[#ff2a2a]">Fintech premium</p>
                        <h1 class="mt-5 text-5xl font-black leading-tight text-white">Controle suas finanças com inteligência.</h1>
                        <p class="mt-5 text-lg leading-8 text-[#b5b5b5]">Organize receitas, despesas, categorias e metas em uma plataforma rápida, segura e visualmente clara.</p>

                        <div class="mt-10 grid gap-4">
                            @foreach (['Acesse seus lançamentos', 'Visualize receitas e despesas', 'Gerencie categorias', 'Acompanhe metas financeiras'] as $item)
                                <div class="flex items-center gap-3 text-sm font-semibold text-zinc-300">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-red-500/20 bg-red-500/10 text-[#ff2a2a]">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M5 13l4 4L19 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </span>
                                    {{ $item }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="absolute bottom-0 left-0 right-0 h-56 bg-[linear-gradient(145deg,transparent_42%,rgba(255,42,42,0.18)_43%,transparent_44%,transparent_49%,rgba(255,42,42,0.12)_50%,transparent_51%)]"></div>
                </section>

                <section class="flex min-h-screen items-center justify-center px-5 py-10">
                    <div class="w-full max-w-md">
                        <div class="mb-8 flex items-center justify-center gap-3 lg:hidden">
                            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-b from-[#ff2a2a] to-[#b80000] font-black text-white shadow-lg shadow-red-500/25">S</span>
                            <span>
                                <span class="block text-lg font-black leading-5 text-white">SYSTEX</span>
                                <span class="block text-[10px] font-bold uppercase tracking-[0.36em] text-[#ff2a2a]">Financeiro</span>
                            </span>
                        </div>

                        <div class="sx-panel rounded-2xl p-6 sm:p-8">
                            {{ $slot }}
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>
