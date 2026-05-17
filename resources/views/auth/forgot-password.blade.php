<x-guest-layout title="Recuperar senha | Systex Financeiro">
    <div class="mb-8">
        <p class="text-sm font-bold uppercase tracking-[0.24em] text-[#ff2a2a]">Recuperação</p>
        <h1 class="mt-3 text-3xl font-black text-white">Esqueceu sua senha?</h1>
        <p class="mt-2 text-sm leading-6 text-[#b5b5b5]">Informe seu e-mail e enviaremos um link seguro para redefinição.</p>
    </div>

    <x-auth-session-status class="mb-5 rounded-xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <div>
            <x-input-label for="email" value="E-mail" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder="voce@systex.com.br" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button class="sx-button w-full">Enviar link de recuperação</button>
    </form>
</x-guest-layout>
