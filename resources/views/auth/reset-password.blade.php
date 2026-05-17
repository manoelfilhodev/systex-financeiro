<x-guest-layout title="Redefinir senha | Systex Financeiro">
    <div class="mb-8">
        <p class="text-sm font-bold uppercase tracking-[0.24em] text-[#ff2a2a]">Nova senha</p>
        <h1 class="mt-3 text-3xl font-black text-white">Redefinir acesso</h1>
        <p class="mt-2 text-sm leading-6 text-[#b5b5b5]">Crie uma nova senha para continuar usando o Systex Financeiro.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" value="E-mail" />
            <x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Senha" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Confirmar senha" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button class="sx-button w-full">Redefinir senha</button>
    </form>
</x-guest-layout>
