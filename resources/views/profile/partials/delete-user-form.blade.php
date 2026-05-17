<section class="space-y-6">
    <header>
        <h2 class="text-lg font-black text-red-300">Excluir conta</h2>
        <p class="mt-2 text-sm leading-6 text-zinc-500">Todos os seus dados serão permanentemente excluídos. Esta ação não pode ser desfeita.</p>
    </header>

    <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">Excluir minha conta</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-xl font-black text-white">Confirmar exclusão</h2>
            <p class="mt-2 text-sm leading-6 text-zinc-500">Digite sua senha para confirmar a exclusão permanente da conta.</p>

            <div class="mt-6">
                <x-input-label for="password" value="Senha" class="sr-only" />
                <x-text-input id="password" name="password" type="password" placeholder="Senha" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">Cancelar</x-secondary-button>
                <x-danger-button>Excluir conta</x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
