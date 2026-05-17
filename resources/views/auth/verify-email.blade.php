<x-guest-layout title="Verificar e-mail | Systex Financeiro">
    <div class="mb-8">
        <p class="text-sm font-bold uppercase tracking-[0.24em] text-[#ff2a2a]">Verificação</p>
        <h1 class="mt-3 text-3xl font-black text-white">Confirme seu e-mail</h1>
        <p class="mt-2 text-sm leading-6 text-[#b5b5b5]">Enviamos um link de confirmação para o e-mail cadastrado. Verifique sua caixa de entrada para ativar a conta.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-5 rounded-xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm font-semibold text-emerald-200">
            Um novo link de verificação foi enviado.
        </div>
    @endif

    <div class="flex flex-col gap-3 sm:flex-row sm:justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="sx-button">Reenviar e-mail</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sx-button-secondary">Sair</button>
        </form>
    </div>
</x-guest-layout>
