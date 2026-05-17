<x-guest-layout title="Criar conta | Systex Financeiro">
    <div class="mb-8">
        <p class="text-sm font-bold uppercase tracking-[0.24em] text-[#ff2a2a]">Comece agora</p>
        <h1 class="mt-3 text-3xl font-black text-white">Criar conta</h1>
        <p class="mt-2 text-sm leading-6 text-[#b5b5b5]">Organize receitas, despesas e metas em um painel premium.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="name" value="Nome" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Seu nome" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="E-mail" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="voce@systex.com.br" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Senha" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Crie uma senha segura" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Confirmar senha" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repita sua senha" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button class="sx-button w-full">Criar conta</button>
    </form>

    <p class="mt-8 text-center text-sm text-zinc-500">
        Já possui conta?
        <a href="{{ route('login') }}" class="font-bold text-[#ff2a2a] transition hover:text-red-300">Entrar</a>
    </p>
</x-guest-layout>
