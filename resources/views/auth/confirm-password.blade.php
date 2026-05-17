<x-guest-layout title="Confirmar senha | Systex Financeiro">
    <div class="mb-8">
        <p class="text-sm font-bold uppercase tracking-[0.24em] text-[#ff2a2a]">Área segura</p>
        <h1 class="mt-3 text-3xl font-black text-white">Confirme sua senha</h1>
        <p class="mt-2 text-sm leading-6 text-[#b5b5b5]">Esta ação exige uma nova confirmação para proteger sua conta.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf
        <div>
            <x-input-label for="password" value="Senha" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <button class="sx-button w-full">Confirmar</button>
    </form>
</x-guest-layout>
