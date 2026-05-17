<section>
    <header class="mb-6">
        <h2 class="text-lg font-black text-white">Informações pessoais</h2>
        <p class="mt-2 text-sm leading-6 text-zinc-500">Atualize seus dados de identificação e contato.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Nome" />
            <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="E-mail" />
            <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 rounded-xl border border-yellow-400/20 bg-yellow-500/10 p-4 text-sm text-yellow-100">
                    Seu e-mail ainda não foi verificado.
                    <button form="send-verification" class="font-bold text-yellow-200 underline underline-offset-4">Reenviar verificação</button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-semibold text-emerald-300">Um novo link de verificação foi enviado.</p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Salvar alterações</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm font-semibold text-emerald-300">Salvo.</p>
            @endif
        </div>
    </form>
</section>
