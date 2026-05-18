@extends('layouts.systex', ['title' => 'Premium | Systex Financeiro', 'heading' => 'Systex Premium', 'eyebrow' => 'Upgrade'])

@section('content')
    @if ($user->subscription_status === 'expired')
        <div class="sx-card mb-6 p-5">
            <h2 class="sx-theme-text text-xl font-black">Seu trial expirou.</h2>
            <p class="sx-theme-muted mt-2 text-sm leading-6">Você voltou para o plano Starter. Seus dados continuam seguros.</p>
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[1fr_420px]">
        <section class="space-y-6">
            <div class="sx-card p-6">
                <span class="sx-badge sx-badge-theme">Plano atual: {{ ucfirst($user->plan) }} / {{ ucfirst($user->subscription_status) }}</span>
                <h2 class="sx-theme-text mt-5 text-3xl font-black">Desbloqueie a camada executiva do Systex Financeiro.</h2>
                <p class="sx-theme-muted mt-3 max-w-3xl text-sm leading-6">O Premium libera gráficos, temas visuais, análises avançadas e futuras features de produtividade financeira.</p>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                <div class="sx-card p-5">
                    <h3 class="sx-theme-text text-xl font-black">Starter</h3>
                    <p class="sx-theme-muted mt-2 text-sm">Gratuito, simples e seguro.</p>
                    <ul class="sx-theme-muted mt-5 space-y-3 text-sm">
                        <li>Dashboard reduzido</li>
                        <li>Lançamentos e categorias</li>
                        <li>Tema padrão Systex</li>
                        <li>Dados preservados</li>
                    </ul>
                </div>

                <div class="sx-card sx-card-hover p-5">
                    <h3 class="sx-theme-text text-xl font-black">Premium</h3>
                    <p class="sx-theme-muted mt-2 text-sm">R$ {{ number_format($premiumPrice, 2, ',', '.') }} por mês, via PIX manual.</p>
                    <ul class="sx-theme-muted mt-5 space-y-3 text-sm">
                        <li>Todos os temas premium</li>
                        <li>Gráficos executivos</li>
                        <li>Saúde financeira e evolução do saldo</li>
                        <li>Futuras features premium</li>
                    </ul>
                </div>
            </div>
        </section>

        <aside class="sx-card h-fit p-6">
            <h2 class="sx-theme-text text-xl font-black">PIX manual</h2>
            <p class="sx-theme-muted mt-2 text-sm leading-6">Faça o PIX, envie o comprovante e aguarde aprovação do administrador.</p>

            <div class="sx-subcard mt-5 flex aspect-square items-center justify-center p-6 text-center">
                <div>
                    <div class="sx-logo-mark mx-auto h-20 w-20 text-2xl">PIX</div>
                    <p class="sx-theme-muted mt-4 text-xs">QRCode PIX manual</p>
                    <p class="sx-theme-text mt-2 break-all text-sm font-bold">{{ $pixKey }}</p>
                </div>
            </div>

            <div class="sx-subcard mt-5 p-4">
                <p class="sx-label">Chave PIX</p>
                <input readonly value="{{ $pixKey }}" class="sx-input">
            </div>

            <form method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label for="comprovante" class="sx-label">Comprovante</label>
                    <input id="comprovante" name="comprovante" type="file" accept=".jpg,.jpeg,.png,.pdf" required class="sx-input py-3">
                    @error('comprovante') <p class="sx-theme-danger mt-2 text-sm font-semibold">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="observacao" class="sx-label">Observação</label>
                    <textarea id="observacao" name="observacao" rows="3" class="sx-textarea" placeholder="Opcional"></textarea>
                </div>

                <button class="sx-button w-full">Já paguei</button>
            </form>

            @if ($payments->isNotEmpty())
                <div class="sx-divider mt-6 border-t pt-5">
                    <h3 class="sx-theme-text font-black">Últimos envios</h3>
                    <div class="mt-3 space-y-2">
                        @foreach ($payments as $payment)
                            <div class="flex items-center justify-between text-sm">
                                <span class="sx-theme-muted">{{ $payment->valor_formatado }}</span>
                                <span class="sx-badge sx-badge-theme">{{ ucfirst($payment->status) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </aside>
    </div>
@endsection
