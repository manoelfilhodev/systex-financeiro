<section>
    <header class="mb-6">
        <h2 class="text-lg font-black text-white">Alterar senha</h2>
        <p class="mt-2 text-sm leading-6 text-zinc-500">Garanta a segurança da sua conta utilizando uma senha forte.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" value="Senha atual" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" placeholder="Digite sua senha atual" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" value="Nova senha" />
            <x-text-input id="update_password_password" name="password" type="password" autocomplete="new-password" placeholder="Digite sua nova senha" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="Confirmar nova senha" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" placeholder="Confirme sua nova senha" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Atualizar senha</x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm font-semibold text-emerald-300">Senha atualizada.</p>
            @endif
        </div>
    </form>
</section>
