<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

use function Livewire\Volt\rules;
use function Livewire\Volt\state;

state([
    'current_password' => '',
    'password' => '',
    'password_confirmation' => '',
]);

rules([
    'current_password' => ['required', 'string', 'current_password'],
    'password' => ['required', 'string', Password::defaults(), 'confirmed'],
]);

$updatePassword = function () {
    try {
        $validated = $this->validate();
    } catch (ValidationException $e) {
        $this->reset('current_password', 'password', 'password_confirmation');

        throw $e;
    }

    Auth::user()->update([
        'password' => Hash::make($validated['password']),
    ]);

    $this->reset('current_password', 'password', 'password_confirmation');

    $this->dispatch('password-updated');
};

?>

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
        <button
            type="submit"
            wire:loading.attr="disabled"
            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-[#F97316] hover:bg-[#ea580c] rounded-lg transition-colors disabled:opacity-70 disabled:cursor-wait min-w-[7rem]"
        >
            <span wire:loading.remove wire:target="updatePassword" class="inline-flex items-center gap-2">
                <x-lucide-save class="w-4 h-4" />
                {{ __('ui.profile_save') }}
            </span>
            <span wire:loading wire:target="updatePassword" class="inline-flex items-center gap-2">
                <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                {{ __('ui.profile_save') }}
            </span>
        </button>
    </form>
</div>
