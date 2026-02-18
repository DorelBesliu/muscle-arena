<div>
    <button
        type="button"
        wire:click="$set('showModal', true)"
        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-500 border border-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all"
    >
        <x-lucide-trash-2 class="w-4 h-4" />
        {{ __('ui.profile_delete_account_button') }}
    </button>

    @if($showModal)
        <x-app-dialog :title="__('ui.profile_delete_dialog_title')" maxWidth="md">
            <x-slot:close>
                <button type="button" wire:click="$set('showModal', false)" class="h-8 w-8 flex items-center justify-center rounded-lg border-2 border-[#333333] text-[#CCCCCC] hover:text-white">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </x-slot:close>
            <form id="delete-user-form" wire:submit="deleteUser">
                @csrf
                <p class="text-sm text-[#CCCCCC]">
                    {{ __('ui.profile_delete_dialog_desc') }}
                </p>
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" class="sr-only" />
                    <x-text-input
                        wire:model="password"
                        id="password"
                        name="password"
                        type="password"
                        class="mt-1 block w-full h-9 text-sm bg-[#000000] border-[#333333] text-white focus:border-[#333333] focus:ring-0 rounded-md"
                        placeholder="{{ __('Password') }}"
                    />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
            </form>
            <x-slot:footer>
                <button
                    type="button"
                    wire:click="$set('showModal', false)"
                    class="h-9 px-4 rounded-xl border-2 border-[#333333] text-sm font-medium text-[#CCCCCC] hover:text-white"
                >
                    {{ __('ui.close') }}
                </button>
                <x-app.components.loading-button
                    type="submit"
                    form="delete-user-form"
                    wire-target="deleteUser"
                    class="h-9 px-4 rounded-xl text-sm font-medium text-white bg-red-500 hover:bg-red-600 disabled:cursor-wait"
                >
                    <x-lucide-trash-2 class="w-4 h-4 shrink-0" />
                    <x-slot:label>{{ __('ui.profile_delete_confirm') }}</x-slot:label>
                </x-app.components.loading-button>
            </x-slot:footer>
        </x-app-dialog>
    @endif
</div>
