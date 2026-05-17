<x-guest-layout title="Entrar | Systex Financeiro">
    <div class="mb-8">
        <p class="text-sm font-bold uppercase tracking-[0.24em] text-[#ff2a2a]">Bem-vindo de volta</p>
        <h1 class="mt-3 text-3xl font-black text-white">Entrar na conta</h1>
        <p class="mt-2 text-sm leading-6 text-[#b5b5b5]">Controle suas finanças com inteligência.</p>
    </div>

    <x-auth-session-status class="mb-5 rounded-xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="E-mail" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="voce@systex.com.br" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Senha" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Digite sua senha" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-4">
            <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-zinc-400">
                <input id="remember_me" type="checkbox" class="rounded border-white/[0.10] bg-[#111111] text-[#ff2a2a] shadow-sm focus:ring-red-500/30" name="remember">
                Lembrar-me
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-zinc-400 transition hover:text-[#ff2a2a]" href="{{ route('password.request') }}">Esqueci minha senha</a>
            @endif
        </div>

        <button class="sx-button w-full">Entrar</button>
    </form>

    <p class="mt-8 text-center text-sm text-zinc-500">
        Ainda não tem uma conta?
        <a href="{{ route('register') }}" class="font-bold text-[#ff2a2a] transition hover:text-red-300">Criar conta</a>
    </p>
</x-guest-layout>
