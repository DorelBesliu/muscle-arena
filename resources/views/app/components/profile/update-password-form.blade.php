<div class="space-y-3">
    <div>
        <x-input-label for="update_password_current_password" :value="__('ui.profile_current_password')" class="text-sm text-white" />
        <x-text-input
            wire:model="current_password"
            id="update_password_current_password"
            name="current_password"
            type="password"
            class="mt-1.5 h-9 w-full text-sm bg-[#000000] border-[#333333] text-white focus:border-[#333333] focus:ring-0 rounded-md"
            autocomplete="current-password"
        />
        <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="update_password_password" :value="__('ui.profile_new_password')" class="text-sm text-white" />
        <x-text-input
            wire:model="password"
            id="update_password_password"
            name="password"
            type="password"
            class="mt-1.5 h-9 w-full text-sm bg-[#000000] border-[#333333] text-white focus:border-[#333333] focus:ring-0 rounded-md"
            autocomplete="new-password"
        />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="update_password_password_confirmation" :value="__('ui.profile_confirm_password')" class="text-sm text-white" />
        <x-text-input
            wire:model="password_confirmation"
            id="update_password_password_confirmation"
            name="password_confirmation"
            type="password"
            class="mt-1.5 h-9 w-full text-sm bg-[#000000] border-[#333333] text-white focus:border-[#333333] focus:ring-0 rounded-md"
            autocomplete="new-password"
        />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
    </div>

    <form wire:submit="updatePassword" class="flex items-center justify-end gap-3 mt-4">
        @csrf
        <x-app.components.loading-button
            type="submit"
            wire-target="updatePassword"
            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-[#F97316] hover:bg-[#ea580c] rounded-lg transition-colors disabled:cursor-wait min-w-[7rem]"
        >
            <x-lucide-save class="w-4 h-4 shrink-0" />
            <x-slot:label>{{ __('ui.profile_save') }}</x-slot:label>
        </x-app.components.loading-button>
    </form>
</div>
