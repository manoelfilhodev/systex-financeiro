@php
    $themePreviews = collect(config('themes', []))
        ->only(['systex-default', 'cyberpunk', 'pink-neon', 'midnight', 'aurora', 'office-clean']);

    $features = [
        ['title' => 'Insights inteligentes', 'description' => 'Mensagens automáticas ajudam você a entender gastos, saldo, economia e evolução do mês.'],
        ['title' => 'Gráficos premium', 'description' => 'Fluxo financeiro, distribuição por categoria, saldo acumulado e saúde financeira em uma tela.'],
        ['title' => 'Temas personalizados', 'description' => 'Escolha uma identidade visual que combine com seu jeito de acompanhar dinheiro.'],
        ['title' => 'Acesso em qualquer dispositivo', 'description' => 'Use no computador, tablet ou celular com uma experiência responsiva e rápida.'],
        ['title' => 'Seus dados seguros', 'description' => 'Autenticação, proteção CSRF e isolamento dos dados por usuário desde a base do sistema.'],
        ['title' => 'Evolução com inteligência financeira', 'description' => 'Uma plataforma pronta para crescer com automações, metas e análises cada vez mais úteis.'],
    ];

    $insights = [
        ['type' => 'positive', 'text' => 'Você economizou 18% este mês.'],
        ['type' => 'trend', 'text' => 'Seu maior gasto foi Alimentação.'],
        ['type' => 'achievement', 'text' => 'Seu saldo cresceu em relação ao mês anterior.'],
        ['type' => 'neutral', 'text' => 'Você está mantendo uma boa saúde financeira.'],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Systex Financeiro | Controle financeiro inteligente</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="theme-systex-default font-sans">
        <main class="sx-shell min-h-screen overflow-hidden">
            <header class="mx-auto flex max-w-7xl items-center justify-between px-5 py-6 sm:px-6 lg:px-8">
                <a href="/" class="flex items-center gap-3">
                    <span class="sx-logo-mark h-12 w-12">S</span>
                    <span>
                        <span class="sx-theme-text block text-lg font-black leading-5">SYSTEX</span>
                        <span class="sx-theme-primary block text-[10px] font-bold uppercase tracking-[0.36em]">Financeiro</span>
                    </span>
                </a>

                <nav class="hidden items-center gap-8 text-sm font-semibold sx-theme-muted md:flex">
                    <a href="#recursos" class="transition hover:text-white">Recursos</a>
                    <a href="#temas" class="transition hover:text-white">Temas</a>
                    <a href="#insights" class="transition hover:text-white">Insights</a>
                    <a href="#beta" class="transition hover:text-white">Beta</a>
                </nav>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="sx-button">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="sx-button-secondary hidden sm:inline-flex">Entrar</a>
                        <a href="{{ route('register') }}" class="sx-button">Começar grátis</a>
                    @endauth
                </div>
            </header>

            <section class="mx-auto grid min-h-[calc(100vh-96px)] max-w-7xl items-center gap-12 px-5 pb-16 pt-8 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
                <div>
                    <p class="sx-theme-primary text-sm font-bold uppercase tracking-[0.28em]">Controle financeiro inteligente</p>
                    <h1 class="sx-theme-text mt-6 max-w-3xl text-5xl font-black leading-[1.02] sm:text-6xl lg:text-7xl">
                        Controle suas finanças com <span class="sx-theme-primary">inteligência.</span>
                    </h1>
                    <p class="sx-theme-muted mt-6 max-w-2xl text-lg leading-8">
                        Organize receitas, despesas, gráficos, temas e insights automáticos em um SaaS financeiro premium feito para decisões rápidas.
                    </p>
                    <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('register') }}" class="sx-button">Começar grátis</a>
                        <x-google-auth-button class="sm:w-auto" />
                        <a href="#demo-preview" class="sx-button-secondary">Ver demonstração</a>
                    </div>
                    <p class="sx-theme-muted mt-5 text-sm font-bold">Teste grátis por 15 dias. Sem cartão.</p>
                </div>

                <div id="demo-preview" class="sx-panel rounded-2xl p-4 sm:p-5">
                    <div class="rounded-xl border p-4 sx-theme-border" style="background: color-mix(in srgb, var(--sx-bg) 86%, transparent);">
                        <div class="flex items-center justify-between border-b pb-4 sx-theme-border">
                            <div class="flex items-center gap-3">
                                <span class="sx-logo-mark h-10 w-10">S</span>
                                <div>
                                    <p class="sx-theme-text font-black">SYSTEX</p>
                                    <p class="sx-theme-primary text-[10px] font-bold uppercase tracking-[0.28em]">Financeiro</p>
                                </div>
                            </div>
                            <span class="sx-badge sx-badge-theme">Maio / 2026</span>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-3">
                            @foreach ([['Entradas', 'R$ 7.650', '+12,5%', 'sx-value-income'], ['Saídas', 'R$ 4.230', '-2,8%', 'sx-value-expense'], ['Saldo', 'R$ 3.420', '+25,1%', 'sx-value-income']] as $card)
                                <div class="sx-card p-4">
                                    <p class="sx-theme-muted text-xs font-bold">{{ $card[0] }}</p>
                                    <p class="sx-theme-text mt-3 text-2xl font-black">{{ $card[1] }}</p>
                                    <p class="{{ $card[3] }} mt-2 text-xs font-bold">{{ $card[2] }} vs mês anterior</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-3 grid gap-3 lg:grid-cols-[1.1fr_0.9fr]">
                            <div class="sx-card p-4">
                                <p class="sx-theme-text mb-3 text-sm font-bold">Fluxo financeiro</p>
                                <div class="flex h-36 items-end gap-2">
                                    @foreach ([44, 68, 52, 82, 61, 96, 74, 88, 64, 105, 72, 91] as $height)
                                        <span class="flex-1 rounded-t-lg" style="height: {{ $height }}%; background: linear-gradient(180deg, var(--sx-primary), transparent); box-shadow: 0 0 24px var(--sx-primary-glow);"></span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="sx-card p-4">
                                <p class="sx-theme-text mb-4 text-sm font-bold">Distribuição</p>
                                <div class="mx-auto h-36 w-36 rounded-full p-4" style="background: conic-gradient(var(--sx-primary) 0 38%, #3b82f6 38% 68%, #22c55e 68% 82%, #52525b 82%);">
                                    <div class="flex h-full w-full items-center justify-center rounded-full" style="background: var(--sx-card);">
                                        <span class="sx-theme-text text-sm font-black">+44%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 grid gap-3 sm:grid-cols-2">
                            <div class="sx-subcard p-4">
                                <p class="sx-theme-muted text-xs font-bold uppercase tracking-[0.16em]">Insight</p>
                                <p class="sx-theme-text mt-2 text-sm font-bold">Você economizou 18% este mês.</p>
                            </div>
                            <div class="sx-subcard p-4">
                                <p class="sx-theme-muted text-xs font-bold uppercase tracking-[0.16em]">Tema ativo</p>
                                <p class="sx-theme-text mt-2 text-sm font-bold">Midnight premium</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="recursos" class="mx-auto max-w-7xl px-5 py-20 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <p class="sx-theme-primary text-sm font-bold uppercase tracking-[0.24em]">Diferenciais</p>
                    <h2 class="sx-theme-text mt-4 text-3xl font-black sm:text-5xl">Uma base premium para acompanhar seu dinheiro de verdade.</h2>
                </div>

                <div class="mt-10 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($features as $feature)
                        <article class="sx-card sx-card-hover p-6">
                            <span class="sx-icon-box mb-5 h-11 w-11">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M5 13l4 4L19 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <h3 class="sx-theme-text text-lg font-black">{{ $feature['title'] }}</h3>
                            <p class="sx-theme-muted mt-3 text-sm leading-6">{{ $feature['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section id="temas" class="mx-auto max-w-7xl px-5 py-20 sm:px-6 lg:px-8">
                <div class="grid gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:items-end">
                    <div>
                        <p class="sx-theme-primary text-sm font-bold uppercase tracking-[0.24em]">Theme Engine</p>
                        <h2 class="sx-theme-text mt-4 text-3xl font-black sm:text-5xl">Seu financeiro com a sua personalidade.</h2>
                        <p class="sx-theme-muted mt-5 text-base leading-7">Escolha entre temas escuros premium, neon, corporativo ou aurora para preparar prints e usar o painel do seu jeito.</p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($themePreviews as $themeKey => $theme)
                            <article class="sx-card p-5">
                                <div class="flex gap-2">
                                    @foreach ($theme['colors'] as $color)
                                        <span class="h-8 flex-1 rounded-lg" style="background: {{ $color }}"></span>
                                    @endforeach
                                </div>
                                <h3 class="sx-theme-text mt-4 font-black">{{ $theme['name'] }}</h3>
                                <p class="sx-theme-muted mt-2 text-xs leading-5">{{ $theme['description'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="insights" class="mx-auto max-w-7xl px-5 py-20 sm:px-6 lg:px-8">
                <div class="sx-card overflow-hidden p-6 sm:p-8">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p class="sx-theme-primary text-sm font-bold uppercase tracking-[0.24em]">Smart Insights</p>
                            <h2 class="sx-theme-text mt-4 text-3xl font-black sm:text-5xl">Sinais simples que deixam o painel mais inteligente.</h2>
                        </div>
                        <span class="sx-badge sx-badge-theme w-fit">Sem IA externa</span>
                    </div>

                    <div class="mt-8 grid gap-4 md:grid-cols-2">
                        @foreach ($insights as $insight)
                            <article class="sx-subcard p-5">
                                <span class="sx-badge {{ $insight['type'] === 'positive' ? 'sx-badge-income' : 'sx-badge-theme' }}">{{ ucfirst($insight['type']) }}</span>
                                <p class="sx-theme-text mt-4 text-lg font-black">{{ $insight['text'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="beta" class="mx-auto max-w-7xl px-5 py-20 sm:px-6 lg:px-8">
                <div class="sx-card grid gap-8 overflow-hidden p-6 sm:p-8 lg:grid-cols-[1fr_auto] lg:items-center">
                    <div>
                        <p class="sx-theme-primary text-sm font-bold uppercase tracking-[0.24em]">Beta Founders</p>
                        <h2 class="sx-theme-text mt-4 text-3xl font-black sm:text-5xl">15 dias grátis para começar sem atrito.</h2>
                        <p class="sx-theme-muted mt-5 max-w-2xl text-base leading-7">Premium por preço de lançamento, sem cartão no cadastro e com evolução contínua da plataforma.</p>
                    </div>
                    <div class="sx-subcard p-5 lg:w-80">
                        <p class="sx-theme-text text-2xl font-black">Premium beta</p>
                        <p class="sx-theme-muted mt-2 text-sm">Preço de lançamento em breve.</p>
                        <a href="{{ route('register') }}" class="sx-button mt-6 w-full">Criar conta grátis</a>
                        <x-google-auth-button class="mt-3" />
                    </div>
                </div>
            </section>

            <section class="mx-auto max-w-7xl px-5 pb-24 pt-10 text-center sm:px-6 lg:px-8">
                <p class="sx-theme-primary text-sm font-bold uppercase tracking-[0.24em]">Comece agora</p>
                <h2 class="sx-theme-text mx-auto mt-4 max-w-3xl text-4xl font-black sm:text-6xl">Pronto para transformar sua vida financeira?</h2>
                <p class="sx-theme-muted mx-auto mt-5 max-w-2xl text-base leading-7">Crie sua conta, registre suas primeiras movimentações e prepare seu dashboard para uma visão mais clara do seu mês.</p>
                <a href="{{ route('register') }}" class="sx-button mt-8">Criar conta grátis</a>
            </section>

            <footer class="border-t px-5 py-8 text-center text-sm sx-theme-border sx-theme-muted">
                © {{ date('Y') }} Systex Financeiro. Todos os direitos reservados.
            </footer>
        </main>
    </body>
</html>
