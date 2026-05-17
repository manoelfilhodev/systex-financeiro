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
    <body class="font-sans">
        <main class="sx-shell min-h-screen overflow-hidden">
            <header class="mx-auto flex max-w-7xl items-center justify-between px-5 py-6 sm:px-6 lg:px-8">
                <a href="/" class="flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-b from-[#ff2a2a] to-[#b80000] font-black text-white shadow-lg shadow-red-500/25">S</span>
                    <span>
                        <span class="block text-lg font-black leading-5 text-white">SYSTEX</span>
                        <span class="block text-[10px] font-bold uppercase tracking-[0.36em] text-[#ff2a2a]">Financeiro</span>
                    </span>
                </a>

                <nav class="hidden items-center gap-8 text-sm font-semibold text-zinc-400 md:flex">
                    <a href="#recursos" class="hover:text-white">Recursos</a>
                    <a href="#beneficios" class="hover:text-white">Benefícios</a>
                    <a href="#seguranca" class="hover:text-white">Segurança</a>
                </nav>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="sx-button">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="sx-button-secondary hidden sm:inline-flex">Entrar</a>
                        <a href="{{ route('register') }}" class="sx-button">Criar conta</a>
                    @endauth
                </div>
            </header>

            <section class="mx-auto grid min-h-[calc(100vh-96px)] max-w-7xl items-center gap-12 px-5 pb-16 pt-8 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.28em] text-[#ff2a2a]">Controle financeiro inteligente</p>
                    <h1 class="mt-6 max-w-3xl text-5xl font-black leading-[1.02] text-white sm:text-6xl lg:text-7xl">
                        Controle suas finanças com <span class="text-[#ff2a2a]">inteligência.</span>
                    </h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-[#b5b5b5]">Visualize receitas, despesas e metas com a clareza de um SaaS premium feito para decisões rápidas.</p>
                    <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('register') }}" class="sx-button">Começar agora</a>
                        <a href="{{ route('login') }}" class="sx-button-secondary">Ver demonstração</a>
                    </div>
                </div>

                <div class="sx-panel rounded-2xl p-4 sm:p-5">
                    <div class="rounded-xl border border-white/[0.06] bg-[#0b0b0b]/80 p-4">
                        <div class="flex items-center justify-between border-b border-white/[0.06] pb-4">
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#ff2a2a] font-black">S</span>
                                <div>
                                    <p class="font-black text-white">SYSTEX</p>
                                    <p class="text-[10px] font-bold uppercase tracking-[0.28em] text-[#ff2a2a]">Financeiro</p>
                                </div>
                            </div>
                            <span class="rounded-lg border border-white/[0.06] bg-white/[0.04] px-3 py-1.5 text-xs font-bold text-zinc-400">Maio / 2026</span>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-3">
                            <div class="sx-card p-4">
                                <p class="text-xs font-bold text-zinc-500">Entradas</p>
                                <p class="mt-3 text-2xl font-black">R$ 7.650</p>
                                <p class="mt-2 text-xs text-emerald-300">+12,5% vs mês anterior</p>
                            </div>
                            <div class="sx-card p-4">
                                <p class="text-xs font-bold text-zinc-500">Saídas</p>
                                <p class="mt-3 text-2xl font-black">R$ 4.230</p>
                                <p class="mt-2 text-xs text-red-300">-2,8% vs mês anterior</p>
                            </div>
                            <div class="sx-card p-4">
                                <p class="text-xs font-bold text-zinc-500">Saldo</p>
                                <p class="mt-3 text-2xl font-black">R$ 3.420</p>
                                <p class="mt-2 text-xs text-emerald-300">+25,1% vs mês anterior</p>
                            </div>
                        </div>

                        <div class="mt-3 grid gap-3 lg:grid-cols-[1.1fr_0.9fr]">
                            <div class="sx-card p-4">
                                <p class="mb-3 text-sm font-bold text-white">Últimos lançamentos</p>
                                <div class="space-y-3 text-sm">
                                    @foreach ([['Salário', 'R$ 5.000,00', 'text-emerald-300'], ['Supermercado', 'R$ 350,00', 'text-red-300'], ['Freelance', 'R$ 1.200,00', 'text-emerald-300']] as $row)
                                        <div class="flex items-center justify-between border-b border-white/[0.06] pb-3 last:border-0 last:pb-0">
                                            <span class="text-zinc-300">{{ $row[0] }}</span>
                                            <span class="{{ $row[2] }} font-bold">{{ $row[1] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="sx-card p-4">
                                <p class="mb-4 text-sm font-bold text-white">Distribuição</p>
                                <div class="mx-auto h-32 w-32 rounded-full bg-[conic-gradient(#ff2a2a_0_38%,#3b82f6_38%_68%,#22c55e_68%_82%,#52525b_82%)] p-4">
                                    <div class="h-full w-full rounded-full bg-[#111111]"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="beneficios" class="mx-auto grid max-w-7xl gap-4 px-5 pb-20 sm:px-6 md:grid-cols-3 lg:px-8">
                @foreach ([['Visualize receitas, despesas e metas', 'Entenda seu mês financeiro em poucos segundos.'], ['Acesse de qualquer lugar', 'Interface responsiva para desktop, tablet e mobile.'], ['Plataforma segura', 'Autenticação, CSRF e isolamento de dados por usuário.']] as $benefit)
                    <div class="sx-card sx-card-hover p-6">
                        <div class="mb-5 flex h-11 w-11 items-center justify-center rounded-xl border border-red-500/20 bg-red-500/10 text-[#ff2a2a]">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M5 13l4 4L19 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <h2 class="text-lg font-black text-white">{{ $benefit[0] }}</h2>
                        <p class="mt-3 text-sm leading-6 text-[#b5b5b5]">{{ $benefit[1] }}</p>
                    </div>
                @endforeach
            </section>

            <footer class="border-t border-white/[0.06] px-5 py-8 text-center text-sm text-zinc-500">
                © {{ date('Y') }} Systex Financeiro. Todos os direitos reservados.
            </footer>
        </main>
    </body>
</html>
